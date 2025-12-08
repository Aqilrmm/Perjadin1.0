<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;
use App\Models\Program\ProgramModel;
use App\Libraries\Logger\ActivityLogger;
use App\Libraries\Notification\NotificationService;
use App\Libraries\Email\EmailService;

/**
 * Approval Program Controller (Updated)
 * 
 * Now uses ActivityLogger, NotificationService, and EmailService
 */
class ApprovalProgramController extends BaseController
{
    protected $programModel;
    protected $logger;
    protected $notificationService;
    protected $emailService;

    public function __construct()
    {
        $this->programModel = new ProgramModel();
        $this->logger = new ActivityLogger();
        $this->notificationService = new NotificationService();
        $this->emailService = new EmailService();
    }

    /**
     * Display program approval page
     */
    public function index()
    {
        $data = [
            'title' => 'Persetujuan Program',
        ];

        return view('kepaladinas/approval/program', $data);
    }

    /**
     * Get programs data for DataTables (AJAX)
     */
    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $data = $this->programModel->getDatatablesData($request);

        // Format data for display
        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_status_badge($row->status);
            $data['data'][$key]->jumlah_anggaran_formatted = format_rupiah($row->jumlah_anggaran);
            $data['data'][$key]->submitted_at_formatted = format_tanggal_waktu($row->submitted_at);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    /**
     * Get program detail (AJAX)
     */
    public function detail($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $program = $this->programModel->getWithRelations($id);

        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Get sisa anggaran
        $program['sisa_anggaran'] = $this->programModel->getSisaAnggaran($id);

        // Log access
        $this->logger->logAccess(user_id(), "Program #{$id}", 'REVIEW');

        return $this->respondSuccess('Program detail', $program);
    }

    /**
     * Approve program
     */
    public function approve($id)
    {
        $program = $this->programModel->find($id);

        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Only pending programs can be approved
        if ($program['status'] != 'pending') {
            return $this->respondError('Hanya program pending yang dapat disetujui', null, 400);
        }

        $catatan = $this->request->getPost('catatan');

        if ($this->programModel->approveProgram($id, user_id(), $catatan)) {
            // Log approval
            $this->logger->logApproval(
                'program',
                $id,
                true,
                $catatan,
                user_id()
            );

            // Send in-app notification to creator
            $this->notificationService->send(
                $program['created_by'],
                'approval',
                'Program Disetujui',
                "Program '{$program['nama_program']}' telah disetujui oleh Kepala Dinas",
                "/kepalabidang/programs"
            );

            // Send email notification
            $userModel = new \App\Models\User\UserModel();
            $creator = $userModel->find($program['created_by']);

            if ($creator && $creator['email']) {
                try {
                    $this->emailService->sendProgramApproved(
                        $creator['email'],
                        $creator['nama'],
                        $program['nama_program']
                    );

                    // Log email sent
                    $this->logger->logNotification(
                        $program['created_by'],
                        'email',
                        'Program Approved Email'
                    );
                } catch (\Exception $e) {
                    // Log email error but don't fail the approval
                    $this->logger->logError($e, user_id(), [
                        'context' => 'send_approval_email',
                        'program_id' => $id
                    ]);
                }
            }

            return $this->respondSuccess('Program berhasil disetujui');
        }

        return $this->respondError('Gagal menyetujui program', null, 500);
    }

    /**
     * Reject program
     */
    public function reject($id)
    {
        $program = $this->programModel->find($id);

        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Only pending programs can be rejected
        if ($program['status'] != 'pending') {
            return $this->respondError('Hanya program pending yang dapat ditolak', null, 400);
        }

        $rules = config('Validation')->rules['catatan'];

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Catatan penolakan wajib diisi minimal 10 karakter', $this->getValidationErrors(), 422);
        }

        $catatan = $this->request->getPost('catatan');

        if ($this->programModel->rejectProgram($id, $catatan)) {
            // Log rejection
            $this->logger->logApproval(
                'program',
                $id,
                false,
                $catatan,
                user_id()
            );

            // Send in-app notification to creator
            $this->notificationService->send(
                $program['created_by'],
                'rejection',
                'Program Ditolak',
                "Program '{$program['nama_program']}' ditolak. Alasan: {$catatan}",
                "/kepalabidang/programs"
            );

            // Send email notification
            $userModel = new \App\Models\User\UserModel();
            $creator = $userModel->find($program['created_by']);

            if ($creator && $creator['email']) {
                try {
                    $this->emailService->sendProgramRejected(
                        $creator['email'],
                        $creator['nama'],
                        $program['nama_program'],
                        $catatan
                    );

                    // Log email sent
                    $this->logger->logNotification(
                        $program['created_by'],
                        'email',
                        'Program Rejected Email'
                    );
                } catch (\Exception $e) {
                    // Log email error but don't fail the rejection
                    $this->logger->logError($e, user_id(), [
                        'context' => 'send_rejection_email',
                        'program_id' => $id
                    ]);
                }
            }

            return $this->respondSuccess('Program berhasil ditolak');
        }

        return $this->respondError('Gagal menolak program', null, 500);
    }

    /**
     * Bulk approve programs
     */
    public function bulkApprove()
    {
        $programIds = $this->request->getPost('program_ids');
        $catatan = $this->request->getPost('catatan');

        if (!$programIds || !is_array($programIds)) {
            return $this->respondError('Program IDs required', null, 400);
        }

        $success = 0;
        $failed = 0;
        $details = [];

        foreach ($programIds as $programId) {
            $program = $this->programModel->find($programId);

            if (!$program || $program['status'] != 'pending') {
                $failed++;
                $details[] = [
                    'id' => $programId,
                    'status' => 'failed',
                    'reason' => 'Invalid status'
                ];
                continue;
            }

            if ($this->programModel->approveProgram($programId, user_id(), $catatan)) {
                $success++;
                $details[] = [
                    'id' => $programId,
                    'status' => 'success'
                ];

                // Send notification
                $this->notificationService->send(
                    $program['created_by'],
                    'approval',
                    'Program Disetujui',
                    "Program '{$program['nama_program']}' telah disetujui",
                    "/kepalabidang/programs"
                );
            } else {
                $failed++;
                $details[] = [
                    'id' => $programId,
                    'status' => 'failed',
                    'reason' => 'Database error'
                ];
            }
        }

        // Log bulk approval
        $this->logger->logBulk('APPROVE', 'programs', $success, user_id());

        return $this->respondSuccess("Berhasil approve {$success} program. Gagal: {$failed}", [
            'success' => $success,
            'failed' => $failed,
            'details' => $details
        ]);
    }

    /**
     * Get approval statistics
     */
    public function statistics()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');

        $stats = [
            'pending' => $this->programModel->where('status', 'pending')->countAllResults(),
            'approved' => $this->programModel->where('status', 'approved')
                ->where('approved_at >=', $startDate)
                ->where('approved_at <=', $endDate . ' 23:59:59')
                ->countAllResults(),
            'rejected' => $this->programModel->where('status', 'rejected')
                ->where('updated_at >=', $startDate)
                ->where('updated_at <=', $endDate . ' 23:59:59')
                ->countAllResults(),
        ];

        // Get approval by bidang
        $byBidang = $this->programModel->select('bidang.nama_bidang, COUNT(*) as count')
            ->join('bidang', 'bidang.id = programs.bidang_id')
            ->where('programs.status', 'approved')
            ->where('programs.approved_at >=', $startDate)
            ->where('programs.approved_at <=', $endDate . ' 23:59:59')
            ->groupBy('programs.bidang_id')
            ->get()
            ->getResult();

        return $this->respondSuccess('Approval statistics', [
            'summary' => $stats,
            'by_bidang' => $byBidang
        ]);
    }

    /**
     * Get action buttons for DataTable
     */
    private function getActionButtons($programId, $status)
    {
        $buttons = '<div class="flex gap-2">';

        // Detail button
        $buttons .= '<button class="btn-detail text-blue-600 hover:text-blue-800" data-id="' . $programId . '" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>';

        // Approve & Reject buttons (only for pending)
        if ($status == 'pending') {
            $buttons .= '<button class="btn-approve text-green-600 hover:text-green-800" data-id="' . $programId . '" title="Approve">
                            <i class="fas fa-check-circle"></i>
                        </button>';
            $buttons .= '<button class="btn-reject text-red-600 hover:text-red-800" data-id="' . $programId . '" title="Reject">
                            <i class="fas fa-times-circle"></i>
                        </button>';
        }

        $buttons .= '</div>';

        return $buttons;
    }
}
