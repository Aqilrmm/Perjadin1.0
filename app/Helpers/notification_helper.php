<?php

/**
 * Notification Helper
 * 
 * Helper functions for notifications and alerts
 */

if (!function_exists('send_notification')) {
    /**
     * Send notification to user
     * 
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return bool
     */
    function send_notification($userId, $type, $title, $message, $link = null)
    {
        $notificationModel = new \App\Models\Notification\NotificationModel();
        
        return $notificationModel->insert([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => 0
        ]);
    }
}

if (!function_exists('send_notification_bulk')) {
    /**
     * Send notification to multiple users
     * 
     * @param array $userIds
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return bool
     */
    function send_notification_bulk($userIds, $type, $title, $message, $link = null)
    {
        $notificationModel = new \App\Models\Notification\NotificationModel();
        
        $data = [];
        foreach ($userIds as $userId) {
            $data[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'is_read' => 0
            ];
        }
        
        return $notificationModel->insertBatch($data);
    }
}

if (!function_exists('get_notification_icon')) {
    /**
     * Get notification icon based on type
     * 
     * @param string $type
     * @return string
     */
    function get_notification_icon($type)
    {
        $icons = [
            'approval' => '<i class="fas fa-check-circle text-green-500"></i>',
            'rejection' => '<i class="fas fa-times-circle text-red-500"></i>',
            'submission' => '<i class="fas fa-paper-plane text-blue-500"></i>',
            'verification' => '<i class="fas fa-check-double text-teal-500"></i>',
            'info' => '<i class="fas fa-info-circle text-blue-500"></i>',
            'warning' => '<i class="fas fa-exclamation-triangle text-yellow-500"></i>',
            'reminder' => '<i class="fas fa-bell text-purple-500"></i>'
        ];
        
        return $icons[$type] ?? '<i class="fas fa-bell text-gray-500"></i>';
    }
}

if (!function_exists('get_notification_color')) {
    /**
     * Get notification color class
     * 
     * @param string $type
     * @return string
     */
    function get_notification_color($type)
    {
        $colors = [
            'approval' => 'bg-green-50 border-green-200',
            'rejection' => 'bg-red-50 border-red-200',
            'submission' => 'bg-blue-50 border-blue-200',
            'verification' => 'bg-teal-50 border-teal-200',
            'info' => 'bg-blue-50 border-blue-200',
            'warning' => 'bg-yellow-50 border-yellow-200',
            'reminder' => 'bg-purple-50 border-purple-200'
        ];
        
        return $colors[$type] ?? 'bg-gray-50 border-gray-200';
    }
}

if (!function_exists('notify_program_approved')) {
    /**
     * Send notification when program is approved
     * 
     * @param int $programId
     * @param int $kepalaBidangId
     * @return bool
     */
    function notify_program_approved($programId, $kepalaBidangId)
    {
        $programModel = new \App\Models\Program\ProgramModel();
        $program = $programModel->find($programId);
        
        return send_notification(
            $kepalaBidangId,
            'approval',
            'Program Disetujui',
            "Program '{$program['nama_program']}' telah disetujui oleh Kepala Dinas",
            "/kepalabidang/programs"
        );
    }
}

if (!function_exists('notify_program_rejected')) {
    /**
     * Send notification when program is rejected
     * 
     * @param int $programId
     * @param int $kepalaBidangId
     * @param string $catatan
     * @return bool
     */
    function notify_program_rejected($programId, $kepalaBidangId, $catatan)
    {
        $programModel = new \App\Models\Program\ProgramModel();
        $program = $programModel->find($programId);
        
        return send_notification(
            $kepalaBidangId,
            'rejection',
            'Program Ditolak',
            "Program '{$program['nama_program']}' ditolak. Catatan: {$catatan}",
            "/kepalabidang/programs"
        );
    }
}

if (!function_exists('notify_sppd_approved')) {
    /**
     * Send notification when SPPD is approved
     * 
     * @param int $sppdId
     * @return bool
     */
    function notify_sppd_approved($sppdId)
    {
        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
        
        $sppd = $sppdModel->find($sppdId);
        $pegawaiList = $sppdPegawaiModel->where('sppd_id', $sppdId)->findAll();
        
        $userIds = array_column($pegawaiList, 'pegawai_id');
        
        // Add Kepala Bidang
        $userIds[] = $sppd['created_by'];
        
        return send_notification_bulk(
            $userIds,
            'approval',
            'SPPD Disetujui',
            "SPPD No. {$sppd['no_sppd']} telah disetujui. Nota Dinas telah dibuat.",
            "/pegawai/sppd/detail/{$sppdId}"
        );
    }
}

if (!function_exists('notify_sppd_submitted')) {
    /**
     * Send notification when SPPD is submitted for verification
     * 
     * @param int $sppdId
     * @return bool
     */
    function notify_sppd_submitted($sppdId)
    {
        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $userModel = new \App\Models\User\UserModel();
        
        $sppd = $sppdModel->find($sppdId);
        
        // Get all keuangan users
        $keuanganUsers = $userModel->where('role', 'keuangan')
                                   ->where('is_active', 1)
                                   ->findAll();
        
        $userIds = array_column($keuanganUsers, 'id');
        
        return send_notification_bulk(
            $userIds,
            'submission',
            'SPPD Perlu Verifikasi',
            "SPPD No. {$sppd['no_sppd']} telah disubmit dan menunggu verifikasi",
            "/keuangan/verifikasi/detail/{$sppdId}"
        );
    }
}

if (!function_exists('notify_sppd_verified')) {
    /**
     * Send notification when SPPD is verified
     * 
     * @param int $sppdId
     * @param int $pegawaiId
     * @return bool
     */
    function notify_sppd_verified($sppdId, $pegawaiId)
    {
        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $sppd = $sppdModel->find($sppdId);
        
        return send_notification(
            $pegawaiId,
            'verification',
            'SPPD Terverifikasi',
            "SPPD No. {$sppd['no_sppd']} telah diverifikasi oleh bagian Keuangan",
            "/pegawai/sppd/detail/{$sppdId}"
        );
    }
}

if (!function_exists('notify_sppd_need_revision')) {
    /**
     * Send notification when SPPD needs revision
     * 
     * @param int $sppdId
     * @param int $pegawaiId
     * @param string $catatan
     * @return bool
     */
    function notify_sppd_need_revision($sppdId, $pegawaiId, $catatan)
    {
        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $sppd = $sppdModel->find($sppdId);
        
        return send_notification(
            $pegawaiId,
            'warning',
            'SPPD Perlu Revisi',
            "SPPD No. {$sppd['no_sppd']} perlu revisi. Catatan: {$catatan}",
            "/pegawai/sppd/detail/{$sppdId}"
        );
    }
}

if (!function_exists('notify_reminder_lppd')) {
    /**
     * Send reminder to fill LPPD
     * 
     * @param int $sppdId
     * @param int $pegawaiId
     * @return bool
     */
    function notify_reminder_lppd($sppdId, $pegawaiId)
    {
        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $sppd = $sppdModel->find($sppdId);
        
        return send_notification(
            $pegawaiId,
            'reminder',
            'Reminder: Isi LPPD',
            "Jangan lupa untuk mengisi LPPD untuk SPPD No. {$sppd['no_sppd']}",
            "/pegawai/lppd/form/{$sppdId}"
        );
    }
}

if (!function_exists('get_flash_message')) {
    /**
     * Get flash message HTML
     * 
     * @return string
     */
    function get_flash_message()
    {
        $session = \Config\Services::session();
        $html = '';
        
        if ($session->getFlashdata('success')) {
            $html .= '<div class="alert alert-success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>' . $session->getFlashdata('success') . '</span>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                      </div>';
        }
        
        if ($session->getFlashdata('error')) {
            $html .= '<div class="alert alert-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>' . $session->getFlashdata('error') . '</span>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                      </div>';
        }
        
        if ($session->getFlashdata('warning')) {
            $html .= '<div class="alert alert-warning bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>' . $session->getFlashdata('warning') . '</span>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                      </div>';
        }
        
        if ($session->getFlashdata('info')) {
            $html .= '<div class="alert alert-info bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>' . $session->getFlashdata('info') . '</span>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                      </div>';
        }
        
        return $html;
    }
}

if (!function_exists('set_success_message')) {
    /**
     * Set success flash message
     * 
     * @param string $message
     * @return void
     */
    function set_success_message($message)
    {
        $session = \Config\Services::session();
        $session->setFlashdata('success', $message);
    }
}

if (!function_exists('set_error_message')) {
    /**
     * Set error flash message
     * 
     * @param string $message
     * @return void
     */
    function set_error_message($message)
    {
        $session = \Config\Services::session();
        $session->setFlashdata('error', $message);
    }
}

if (!function_exists('set_warning_message')) {
    /**
     * Set warning flash message
     * 
     * @param string $message
     * @return void
     */
    function set_warning_message($message)
    {
        $session = \Config\Services::session();
        $session->setFlashdata('warning', $message);
    }
}

if (!function_exists('set_info_message')) {
    /**
     * Set info flash message
     * 
     * @param string $message
     * @return void
     */
    function set_info_message($message)
    {
        $session = \Config\Services::session();
        $session->setFlashdata('info', $message);
    }
}

if (!function_exists('get_unread_notification_count')) {
    /**
     * Get count of unread notifications for current user
     * 
     * @return int
     */
    function get_unread_notification_count()
    {
        if (!is_logged_in()) {
            return 0;
        }
        
        $notificationModel = new \App\Models\Notification\NotificationModel();
        
        return $notificationModel->where('user_id', user_id())
                                  ->where('is_read', 0)
                                  ->countAllResults();
    }
}

if (!function_exists('get_recent_notifications')) {
    /**
     * Get recent notifications for current user
     * 
     * @param int $limit
     * @return array
     */
    function get_recent_notifications($limit = 5)
    {
        if (!is_logged_in()) {
            return [];
        }
        
        $notificationModel = new \App\Models\Notification\NotificationModel();
        
        return $notificationModel->where('user_id', user_id())
                                  ->orderBy('created_at', 'DESC')
                                  ->limit($limit)
                                  ->findAll();
    }
}

if (!function_exists('mark_notification_read')) {
    /**
     * Mark notification as read
     * 
     * @param int $notificationId
     * @return bool
     */
    function mark_notification_read($notificationId)
    {
        $notificationModel = new \App\Models\Notification\NotificationModel();
        
        return $notificationModel->update($notificationId, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }
}

if (!function_exists('send_email_notification')) {
    /**
     * Send email notification (using PHPMailer)
     * 
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return bool
     */
    function send_email_notification($to, $subject, $message)
    {
        $email = \Config\Services::email();
        
        $email->setFrom('noreply@perjadin.com', 'Aplikasi Perjadin');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);
        
        return $email->send();
    }
}