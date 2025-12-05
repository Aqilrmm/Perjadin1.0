<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;

/**
 * DataTable Controller
 * 
 * Centralized controller for DataTables server-side processing
 * with common configurations and utilities
 */
class DataTableController extends BaseController
{
    /**
     * Get DataTable configuration
     */
    public function getConfig()
    {
        $type = $this->request->getGet('type');

        $configs = [
            'users' => [
                'ajax_url' => base_url('api/superadmin/users/datatable'),
                'columns' => [
                    ['data' => 'nip_nik', 'title' => 'NIP/NIK', 'searchable' => true],
                    ['data' => 'nama_lengkap', 'title' => 'Nama', 'searchable' => true],
                    ['data' => 'jabatan', 'title' => 'Jabatan', 'searchable' => true],
                    ['data' => 'bidang', 'title' => 'Bidang', 'searchable' => true],
                    ['data' => 'role_badge', 'title' => 'Role', 'searchable' => false, 'orderable' => false],
                    ['data' => 'status_badge', 'title' => 'Status', 'searchable' => false, 'orderable' => false],
                    ['data' => 'action', 'title' => 'Action', 'searchable' => false, 'orderable' => false],
                ],
                'order' => [[0, 'asc']],
            ],
            'bidang' => [
                'ajax_url' => base_url('api/superadmin/bidang/datatable'),
                'columns' => [
                    ['data' => 'kode_bidang', 'title' => 'Kode', 'searchable' => true],
                    ['data' => 'nama_bidang', 'title' => 'Nama Bidang', 'searchable' => true],
                    ['data' => 'jumlah_pegawai', 'title' => 'Jumlah Pegawai', 'searchable' => false],
                    ['data' => 'status_badge', 'title' => 'Status', 'searchable' => false, 'orderable' => false],
                    ['data' => 'action', 'title' => 'Action', 'searchable' => false, 'orderable' => false],
                ],
                'order' => [[0, 'asc']],
            ],
            'programs' => [
                'ajax_url' => base_url('api/kepalabidang/programs/datatable'),
                'columns' => [
                    ['data' => 'kode_program', 'title' => 'Kode', 'searchable' => true],
                    ['data' => 'nama_program', 'title' => 'Nama Program', 'searchable' => true],
                    ['data' => 'tahun_anggaran', 'title' => 'Tahun', 'searchable' => false],
                    ['data' => 'jumlah_anggaran_formatted', 'title' => 'Anggaran', 'searchable' => false],
                    ['data' => 'sisa_anggaran_formatted', 'title' => 'Sisa', 'searchable' => false],
                    ['data' => 'status_badge', 'title' => 'Status', 'searchable' => false, 'orderable' => false],
                    ['data' => 'action', 'title' => 'Action', 'searchable' => false, 'orderable' => false],
                ],
                'order' => [[0, 'desc']],
            ],
            'kegiatan' => [
                'ajax_url' => base_url('api/kepalabidang/kegiatan/datatable'),
                'columns' => [
                    ['data' => 'kode_kegiatan', 'title' => 'Kode', 'searchable' => true],
                    ['data' => 'nama_kegiatan', 'title' => 'Nama Kegiatan', 'searchable' => true],
                    ['data' => 'nama_program', 'title' => 'Program', 'searchable' => true],
                    ['data' => 'anggaran_formatted', 'title' => 'Anggaran', 'searchable' => false],
                    ['data' => 'sisa_formatted', 'title' => 'Sisa', 'searchable' => false],
                    ['data' => 'status_badge', 'title' => 'Status', 'searchable' => false, 'orderable' => false],
                    ['data' => 'action', 'title' => 'Action', 'searchable' => false, 'orderable' => false],
                ],
                'order' => [[0, 'desc']],
            ],
            'subkegiatan' => [
                'ajax_url' => base_url('api/kepalabidang/subkegiatan/datatable'),
                'columns' => [
                    ['data' => 'kode_sub_kegiatan', 'title' => 'Kode', 'searchable' => true],
                    ['data' => 'nama_sub_kegiatan', 'title' => 'Nama Sub Kegiatan', 'searchable' => true],
                    ['data' => 'nama_kegiatan', 'title' => 'Kegiatan', 'searchable' => true],
                    ['data' => 'anggaran_formatted', 'title' => 'Anggaran', 'searchable' => false],
                    ['data' => 'sisa_formatted', 'title' => 'Sisa', 'searchable' => false],
                    ['data' => 'status_badge', 'title' => 'Status', 'searchable' => false, 'orderable' => false],
                    ['data' => 'action', 'title' => 'Action', 'searchable' => false, 'orderable' => false],
                ],
                'order' => [[0, 'desc']],
            ],
            'sppd' => [
                'ajax_url' => base_url('api/kepalabidang/sppd/datatable'),
                'columns' => [
                    ['data' => 'no_sppd', 'title' => 'No. SPPD', 'searchable' => true],
                    ['data' => 'nama_program', 'title' => 'Program', 'searchable' => true],
                    ['data' => 'tempat_tujuan', 'title' => 'Tujuan', 'searchable' => true],
                    ['data' => 'tanggal_formatted', 'title' => 'Tanggal', 'searchable' => false],
                    ['data' => 'tipe_badge', 'title' => 'Tipe', 'searchable' => false, 'orderable' => false],
                    ['data' => 'jumlah_pegawai', 'title' => 'Pegawai', 'searchable' => false],
                    ['data' => 'estimasi_biaya_formatted', 'title' => 'Estimasi', 'searchable' => false],
                    ['data' => 'status_badge', 'title' => 'Status', 'searchable' => false, 'orderable' => false],
                    ['data' => 'action', 'title' => 'Action', 'searchable' => false, 'orderable' => false],
                ],
                'order' => [[3, 'desc']],
            ],
            'logs' => [
                'ajax_url' => base_url('api/superadmin/logs/datatable'),
                'columns' => [
                    ['data' => 'id', 'title' => 'ID', 'searchable' => false],
                    ['data' => 'user_display', 'title' => 'User', 'searchable' => true],
                    ['data' => 'action_badge', 'title' => 'Action', 'searchable' => true],
                    ['data' => 'description', 'title' => 'Description', 'searchable' => true],
                    ['data' => 'ip_address', 'title' => 'IP Address', 'searchable' => true],
                    ['data' => 'created_at_formatted', 'title' => 'Timestamp', 'searchable' => false],
                ],
                'order' => [[0, 'desc']],
            ],
            'blocked_users' => [
                'ajax_url' => base_url('api/superadmin/blocked/datatable'),
                'columns' => [
                    ['data' => 'nip_nik', 'title' => 'NIP/NIK', 'searchable' => true],
                    ['data' => 'nama_lengkap', 'title' => 'Nama', 'searchable' => true],
                    ['data' => 'bidang', 'title' => 'Bidang', 'searchable' => true],
                    ['data' => 'blocked_reason', 'title' => 'Alasan', 'searchable' => true],
                    ['data' => 'blocked_by_name', 'title' => 'Diblokir Oleh', 'searchable' => false],
                    ['data' => 'blocked_at_formatted', 'title' => 'Waktu', 'searchable' => false],
                    ['data' => 'action', 'title' => 'Action', 'searchable' => false, 'orderable' => false],
                ],
                'order' => [[5, 'desc']],
            ],
            'verifikasi' => [
                'ajax_url' => base_url('api/keuangan/verifikasi/datatable'),
                'columns' => [
                    ['data' => 'no_sppd', 'title' => 'No. SPPD', 'searchable' => true],
                    ['data' => 'nama_bidang', 'title' => 'Bidang', 'searchable' => true],
                    ['data' => 'tempat_tujuan', 'title' => 'Tujuan', 'searchable' => true],
                    ['data' => 'jumlah_pegawai', 'title' => 'Pegawai', 'searchable' => false],
                    ['data' => 'estimasi_biaya_formatted', 'title' => 'Estimasi', 'searchable' => false],
                    ['data' => 'submitted_at_formatted', 'title' => 'Waktu Submit', 'searchable' => false],
                    ['data' => 'status_badge', 'title' => 'Status', 'searchable' => false, 'orderable' => false],
                    ['data' => 'action', 'title' => 'Action', 'searchable' => false, 'orderable' => false],
                ],
                'order' => [[5, 'asc']],
            ],
        ];

        if (!isset($configs[$type])) {
            return $this->respondError('Invalid DataTable type', null, 400);
        }

        return $this->respondSuccess('DataTable configuration', $configs[$type]);
    }

    /**
     * Get DataTable language (Indonesian)
     */
    public function getLanguage()
    {
        $language = [
            'processing' => 'Memproses...',
            'search' => 'Cari:',
            'lengthMenu' => 'Tampilkan _MENU_ data',
            'info' => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            'infoEmpty' => 'Menampilkan 0 sampai 0 dari 0 data',
            'infoFiltered' => '(disaring dari _MAX_ total data)',
            'infoPostFix' => '',
            'loadingRecords' => 'Memuat...',
            'zeroRecords' => 'Data tidak ditemukan',
            'emptyTable' => 'Tidak ada data tersedia',
            'paginate' => [
                'first' => 'Pertama',
                'previous' => 'Sebelumnya',
                'next' => 'Selanjutnya',
                'last' => 'Terakhir',
            ],
            'aria' => [
                'sortAscending' => ': aktifkan untuk mengurutkan kolom ascending',
                'sortDescending' => ': aktifkan untuk mengurutkan kolom descending',
            ],
        ];

        return $this->respondSuccess('DataTable language', $language);
    }

    /**
     * Get common DataTable options
     */
    public function getCommonOptions()
    {
        $options = [
            'processing' => true,
            'serverSide' => true,
            'responsive' => true,
            'autoWidth' => false,
            'pageLength' => 10,
            'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
            'dom' => '<"flex flex-col md:flex-row justify-between items-center mb-4"<"mb-2 md:mb-0"l><"mb-2 md:mb-0"f>>rtip',
            'drawCallback' => 'function() { 
                // Re-initialize tooltips or other plugins after draw
                if (typeof initTooltips === "function") {
                    initTooltips();
                }
            }',
        ];

        return $this->respondSuccess('Common options', $options);
    }

    /**
     * Export DataTable data
     */
    public function export()
    {
        $type = $this->request->getPost('type');
        $format = $this->request->getPost('format') ?: 'csv';
        $filters = $this->request->getPost('filters') ?: [];

        if (!$type) {
            return $this->respondError('Type required', null, 400);
        }

        // Get model based on type
        $model = $this->getModelByType($type);
        
        if (!$model) {
            return $this->respondError('Invalid type', null, 400);
        }

        // Build query with filters
        $builder = $model->builder();
        
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $builder->where($key, $value);
            }
        }

        $data = $builder->get()->getResult();

        if ($format === 'csv') {
            return $this->exportCSV($data, $type);
        } elseif ($format === 'excel') {
            return $this->exportExcel($data, $type);
        } elseif ($format === 'pdf') {
            return $this->exportPDF($data, $type);
        }

        return $this->respondError('Invalid format', null, 400);
    }

    /**
     * Get model by type
     */
    protected function getModelByType($type)
    {
        $models = [
            'users' => '\App\Models\User\UserModel',
            'bidang' => '\App\Models\Bidang\BidangModel',
            'programs' => '\App\Models\Program\ProgramModel',
            'kegiatan' => '\App\Models\Program\KegiatanModel',
            'subkegiatan' => '\App\Models\Program\SubKegiatanModel',
            'sppd' => '\App\Models\SPPD\SPPDModel',
            'logs' => '\App\Models\Log\SecurityLogModel',
        ];

        if (isset($models[$type])) {
            return new $models[$type]();
        }

        return null;
    }

    /**
     * Export to CSV
     */
    protected function exportCSV($data, $type)
    {
        $filename = $type . '_export_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        if (!empty($data)) {
            // Get headers from first row
            $headers = array_keys((array)$data[0]);
            fputcsv($output, $headers);
            
            // Write data rows
            foreach ($data as $row) {
                fputcsv($output, (array)$row);
            }
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export to Excel (placeholder)
     */
    protected function exportExcel($data, $type)
    {
        // TODO: Implement using PhpSpreadsheet
        return $this->respondError('Excel export not implemented yet', null, 501);
    }

    /**
     * Export to PDF (placeholder)
     */
    protected function exportPDF($data, $type)
    {
        // TODO: Implement using mPDF
        return $this->respondError('PDF export not implemented yet', null, 501);
    }

    /**
     * Get filter options for specific table
     */
    public function getFilterOptions()
    {
        $type = $this->request->getGet('type');

        $options = [];

        switch ($type) {
            case 'users':
                $options = [
                    'roles' => [
                        ['value' => 'superadmin', 'text' => 'Super Admin'],
                        ['value' => 'kepaladinas', 'text' => 'Kepala Dinas'],
                        ['value' => 'kepalabidang', 'text' => 'Kepala Bidang'],
                        ['value' => 'pegawai', 'text' => 'Pegawai'],
                        ['value' => 'keuangan', 'text' => 'Keuangan'],
                    ],
                    'bidang' => (new \App\Models\Bidang\BidangModel())->getActiveOptions(),
                    'status' => [
                        ['value' => 'active', 'text' => 'Active'],
                        ['value' => 'inactive', 'text' => 'Inactive'],
                        ['value' => 'blocked', 'text' => 'Blocked'],
                    ],
                ];
                break;

            case 'programs':
            case 'kegiatan':
            case 'subkegiatan':
                $options = [
                    'status' => [
                        ['value' => 'draft', 'text' => 'Draft'],
                        ['value' => 'pending', 'text' => 'Pending'],
                        ['value' => 'approved', 'text' => 'Approved'],
                        ['value' => 'rejected', 'text' => 'Rejected'],
                    ],
                ];
                break;

            case 'sppd':
                $options = [
                    'status' => [
                        ['value' => 'draft', 'text' => 'Draft'],
                        ['value' => 'pending', 'text' => 'Pending'],
                        ['value' => 'approved', 'text' => 'Approved'],
                        ['value' => 'rejected', 'text' => 'Rejected'],
                        ['value' => 'submitted', 'text' => 'Submitted'],
                        ['value' => 'need_revision', 'text' => 'Need Revision'],
                        ['value' => 'verified', 'text' => 'Verified'],
                        ['value' => 'closed', 'text' => 'Closed'],
                    ],
                    'tipe_perjalanan' => [
                        ['value' => 'Dalam Daerah', 'text' => 'Dalam Daerah'],
                        ['value' => 'Luar Daerah Dalam Provinsi', 'text' => 'Luar Daerah Dalam Provinsi'],
                        ['value' => 'Luar Daerah Luar Provinsi', 'text' => 'Luar Daerah Luar Provinsi'],
                    ],
                ];
                break;

            case 'logs':
                $logModel = new \App\Models\Log\SecurityLogModel();
                $options = [
                    'actions' => $logModel->select('DISTINCT action')->orderBy('action', 'ASC')->findAll(),
                ];
                break;
        }

        return $this->respondSuccess('Filter options', $options);
    }

    /**
     * Bulk delete (soft delete)
     */
    public function bulkDelete()
    {
        $type = $this->request->getPost('type');
        $ids = $this->request->getPost('ids');

        if (!$type || !$ids || !is_array($ids)) {
            return $this->respondError('Invalid request', null, 400);
        }

        $model = $this->getModelByType($type);
        
        if (!$model) {
            return $this->respondError('Invalid type', null, 400);
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($ids as $id) {
            if ($model->delete($id)) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        $this->logActivity('BULK_DELETE', "Bulk deleted {$successCount} {$type}. Failed: {$failedCount}");

        return $this->respondSuccess("Berhasil menghapus {$successCount} data. Gagal: {$failedCount}");
    }
}