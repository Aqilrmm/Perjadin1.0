<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;

// ========================================
// MY SPPD CONTROLLER
// ========================================

class MySPPDController extends BaseController
{
    protected $sppdModel;
    protected $sppdPegawaiModel;

    public function __construct()
    {
        $this->sppdModel = new \App\Models\SPPD\SPPDModel();
        $this->sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
    }

    public function index()
    {
        $data = [
            'title' => 'SPPD Saya',
        ];

        return view('pegawai/sppd/index', $data);
    }

    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $pegawaiId = user_id();

        // Custom query for pegawai's SPPD
        $builder = $this->sppdModel->builder();
        $builder->select('sppd.*, bidang.nama_bidang')
                ->join('sppd_pegawai', 'sppd_pegawai.sppd_id = sppd.id')
                ->join('bidang', 'bidang.id = sppd.bidang_id', 'left')
                ->where('sppd_pegawai.pegawai_id', $pegawaiId)
                ->where('sppd.deleted_at', null);

        // Apply filters
        if (isset($request['filters']['status']) && $request['filters']['status']) {
            $builder->where('sppd.status', $request['filters']['status']);
        }

        $totalFiltered = $builder->countAllResults(false);

        $data = $builder->orderBy('sppd.tanggal_berangkat', 'DESC')
                        ->limit($request['length'], $request['start'])
                        ->get()
                        ->getResult();

        foreach ($data as $key => $row) {
            $data[$key]->status_badge = get_sppd_status_badge($row->status);
            $data[$key]->tipe_badge = get_tipe_perjalanan_badge($row->tipe_perjalanan);
            $data[$key]->tanggal_formatted = format_tanggal($row->tanggal_berangkat, false);
            $data[$key]->action = $this->getActionButtons($row->id, $row->status, $row->tanggal_kembali);
        }

        return $this->respond([
            'draw' => intval($request['draw']),
            'recordsTotal' => $this->sppdPegawaiModel->where('pegawai_id', $pegawaiId)->countAllResults(),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function detail($id)
    {
        $sppd = $this->sppdModel->getWithRelations($id);

        if (!$sppd) {
            return redirect()->to('/pegawai/sppd')->with('error', 'SPPD tidak ditemukan');
        }

        // Check if user is in SPPD
        if (!$this->sppdPegawaiModel->isPegawaiInSppd($id, user_id())) {
            return redirect()->to('/pegawai/sppd')->with('error', 'Unauthorized');
        }

        $lppdModel = new \App\Models\SPPD\LPPDModel();
        $kwitansiModel = new \App\Models\SPPD\KwitansiModel();

        $data = [
            'title' => 'Detail SPPD',
            'sppd' => $sppd,
            'lppd' => $lppdModel->getBySppd($id),
            'kwitansi' => $kwitansiModel->getBySppd($id),
            'pegawai_list' => $this->sppdPegawaiModel->getPegawaiBySppdId($id),
        ];

        return view('pegawai/sppd/detail', $data);
    }

    private function getActionButtons($sppdId, $status, $tanggalKembali)
    {
        $buttons = '<div class="flex gap-2">';
        $buttons .= '<button class="btn-detail text-blue-600" data-id="'.$sppdId.'"><i class="fas fa-eye"></i></button>';

        if ($status == 'approved') {
            $buttons .= '<button class="btn-nota text-purple-600" data-id="'.$sppdId.'"><i class="fas fa-file-pdf"></i></button>';
            
            // Show fill LPPD button if after tanggal kembali
            if (strtotime(date('Y-m-d')) >= strtotime($tanggalKembali)) {
                $buttons .= '<button class="btn-lppd text-green-600" data-id="'.$sppdId.'"><i class="fas fa-edit"></i></button>';
            }
        }

        $buttons .= '</div>';
        return $buttons;
    }
}