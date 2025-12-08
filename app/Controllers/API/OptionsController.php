<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;

class OptionsController extends BaseController
{
    public function program()
    {
        $search = $this->request->getGet('q') ?? $this->request->getGet('search');
        $bidangId = $this->request->getGet('bidang_id');

        $model = new \App\Models\Program\ProgramModel();

        $builder = $model->where('status', 'approved')->where('deleted_at', null);
        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        if ($search) {
            $builder->groupStart()
                ->like('kode_program', $search)
                ->orLike('nama_program', $search)
                ->groupEnd();
        }

        $rows = $builder->select('id, kode_program, nama_program')->findAll();

        $results = [];
        foreach ($rows as $r) {
            $results[] = ['id' => $r['id'], 'text' => $r['kode_program'] . ' - ' . $r['nama_program']];
        }

        return $this->respond(['results' => $results]);
    }

    public function kegiatan()
    {
        $search = $this->request->getGet('q') ?? $this->request->getGet('search');
        $programId = $this->request->getGet('program_id');

        $model = new \App\Models\Program\KegiatanModel();

        $builder = $model->where('status', 'approved')->where('deleted_at', null);
        if ($programId) {
            $builder->where('program_id', $programId);
        }

        if ($search) {
            $builder->groupStart()
                ->like('kode_kegiatan', $search)
                ->orLike('nama_kegiatan', $search)
                ->groupEnd();
        }

        $rows = $builder->select('id, kode_kegiatan, nama_kegiatan')->findAll();

        $results = [];
        foreach ($rows as $r) {
            $results[] = ['id' => $r['id'], 'text' => $r['kode_kegiatan'] . ' - ' . $r['nama_kegiatan']];
        }

        return $this->respond(['results' => $results]);
    }

    public function subkegiatan()
    {
        $search = $this->request->getGet('q') ?? $this->request->getGet('search');
        $kegiatanId = $this->request->getGet('kegiatan_id');

        $model = new \App\Models\Program\SubKegiatanModel();

        $builder = $model->where('status', 'approved')->where('deleted_at', null);
        if ($kegiatanId) {
            $builder->where('kegiatan_id', $kegiatanId);
        }

        if ($search) {
            $builder->groupStart()
                ->like('kode_sub_kegiatan', $search)
                ->orLike('nama_sub_kegiatan', $search)
                ->groupEnd();
        }

        $rows = $builder->select('id, kode_sub_kegiatan, nama_sub_kegiatan')->findAll();

        $results = [];
        foreach ($rows as $r) {
            $results[] = ['id' => $r['id'], 'text' => $r['kode_sub_kegiatan'] . ' - ' . $r['nama_sub_kegiatan']];
        }

        return $this->respond(['results' => $results]);
    }
}
