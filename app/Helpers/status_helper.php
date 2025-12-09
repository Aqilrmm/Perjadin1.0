<?php

/**
 * Status Helper
 * 
 * Helper functions for status display and badges
 */

if (!function_exists('get_status_badge')) {
    /**
     * Get status badge HTML
     * 
     * @param string $status
     * @param string $type (program|kegiatan|sppd|user)
     * @return string
     */
    function get_status_badge($status, $type = 'general')
    {
        $badges = [
            'draft' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>',
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'approved' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>',
            'rejected' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>',
            'submitted' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Submitted</span>',
            'need_revision' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Perlu Revisi</span>',
            'verified' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800">Verified</span>',
            'closed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Selesai</span>',
            'active' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>',
            'inactive' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>',
            'blocked' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Blocked</span>',
            'general' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">' . ucfirst($status) . '</span>'
        ];
        
        return $badges[strtolower($status)] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">' . ucfirst($status) . '</span>';
    }
}

if (!function_exists('get_status_color')) {
    /**
     * Get status color class
     * 
     * @param string $status
     * @return string
     */
    function get_status_color($status)
    {
        $colors = [
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'submitted' => 'blue',
            'need_revision' => 'orange',
            'verified' => 'teal',
            'closed' => 'gray',
            'active' => 'green',
            'inactive' => 'gray',
            'blocked' => 'red'
        ];
        
        return $colors[strtolower($status)] ?? 'gray';
    }
}

if (!function_exists('get_status_icon')) {
    /**
     * Get status icon (Font Awesome)
     * 
     * @param string $status
     * @return string
     */
    function get_status_icon($status)
    {
        $icons = [
            'draft' => 'fa-file-alt',
            'pending' => 'fa-clock',
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            'submitted' => 'fa-paper-plane',
            'need_revision' => 'fa-exclamation-circle',
            'verified' => 'fa-check-double',
            'closed' => 'fa-archive',
            'active' => 'fa-check',
            'inactive' => 'fa-ban',
            'blocked' => 'fa-lock'
        ];
        
        $icon = $icons[strtolower($status)] ?? 'fa-circle';
        
        return '<i class="fas ' . $icon . '"></i>';
    }
}

if (!function_exists('get_status_text')) {
    /**
     * Get status display text in Indonesian
     * 
     * @param string $status
     * @return string
     */
    function get_status_text($status)
    {
        $texts = [
            'draft' => 'Draft',
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'submitted' => 'Sudah Disubmit',
            'need_revision' => 'Perlu Revisi',
            'verified' => 'Terverifikasi',
            'closed' => 'Selesai',
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'blocked' => 'Diblokir'
        ];
        
        return $texts[strtolower($status)] ?? ucfirst($status);
    }
}

if (!function_exists('get_sppd_status_badge')) {
    /**
     * Get SPPD status badge with icon
     * 
     * @param string $status
     * @return string
     */
    function get_sppd_status_badge($status)
    {
        $badges = [
            'draft' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-file-alt mr-1"></i> Draft</span>',
            'pending' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-clock mr-1"></i> Menunggu Persetujuan</span>',
            'approved' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Disetujui</span>',
            'rejected' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>',
            'submitted' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-paper-plane mr-1"></i> Menunggu Verifikasi</span>',
            'need_revision' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-exclamation-circle mr-1"></i> Perlu Revisi</span>',
            'verified' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800"><i class="fas fa-check-double mr-1"></i> Terverifikasi</span>',
            'closed' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-archive mr-1"></i> Selesai</span>'
        ];
        
        return $badges[strtolower($status)] ?? get_status_badge($status);
    }
}

if (!function_exists('get_tipe_perjalanan_badge')) {
    /**
     * Get trip type badge
     * 
     * @param string $tipe
     * @return string
     */
    function get_tipe_perjalanan_badge($tipe)
    {
        $badges = [
            'Dalam Daerah' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-map-marker-alt mr-1"></i> Dalam Daerah</span>',
            'Luar Daerah Dalam Provinsi' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-map-marked-alt mr-1"></i> Luar Daerah Dalam Provinsi</span>',
            'Luar Daerah Luar Provinsi' => '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-plane mr-1"></i> Luar Daerah Luar Provinsi</span>'
        ];
        
        return $badges[$tipe] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">' . $tipe . '</span>';
    }
}

if (!function_exists('get_user_status_badge')) {
    /**
     * Get user status badge
     * 
     * @param bool $isActive
     * @param bool $isBlocked
     * @return string
     */
    function get_user_status_badge($isActive, $isBlocked = false)
    {
        if ($isBlocked) {
            return '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-lock mr-1"></i> Blocked</span>';
        }
        
        if ($isActive) {
            return '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i> Active</span>';
        }
        
        return '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-ban mr-1"></i> Inactive</span>';
    }
}

if (!function_exists('get_jenis_pegawai_badge')) {
    /**
     * Get employee type badge
     * 
     * @param string $jenis
     * @return string
     */
    function get_jenis_pegawai_badge($jenis)
    {
        $badges = [
            'ASN' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">ASN</span>',
            'Non-ASN' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Non-ASN</span>'
        ];
        
        return $badges[$jenis] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">' . $jenis . '</span>';
    }
}

if (!function_exists('get_anggaran_badge')) {
    /**
     * Get budget status badge
     * 
     * @param float $jumlahAnggaran
     * @param float $sisaAnggaran
     * @return string
     */
    function get_anggaran_badge($jumlahAnggaran, $sisaAnggaran)
    {
        if ($jumlahAnggaran == 0) {
            return '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No Budget</span>';
        }
        
        $percentage = ($sisaAnggaran / $jumlahAnggaran) * 100;
        
        if ($percentage > 50) {
            return '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-battery-full mr-1"></i> ' . number_format($percentage, 0) . '%</span>';
        } elseif ($percentage > 20) {
            return '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-battery-half mr-1"></i> ' . number_format($percentage, 0) . '%</span>';
        } else {
            return '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-battery-quarter mr-1"></i> ' . number_format($percentage, 0) . '%</span>';
        }
    }
}

if (!function_exists('get_progress_bar')) {
    /**
     * Get progress bar HTML
     * 
     * @param float $current
     * @param float $total
     * @return string
     */
    function get_progress_bar($current, $total)
    {
        if ($total == 0) {
            return '<div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-gray-400 h-2 rounded-full" style="width: 0%"></div></div>';
        }
        
        $percentage = ($current / $total) * 100;
        $percentage = min(100, $percentage); // Cap at 100%
        
        $color = 'bg-green-500';
        if ($percentage > 80) {
            $color = 'bg-red-500';
        } elseif ($percentage > 50) {
            $color = 'bg-yellow-500';
        }
        
        return '<div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="' . $color . ' h-2 rounded-full" style="width: ' . $percentage . '%"></div>
                </div>
                <div class="text-xs text-gray-600 mt-1">' . number_format($percentage, 1) . '%</div>';
    }
}

if (!function_exists('get_priority_badge')) {
    /**
     * Get priority badge
     * 
     * @param string $priority (low|medium|high)
     * @return string
     */
    function get_priority_badge($priority)
    {
        $badges = [
            'low' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Rendah</span>',
            'medium' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Sedang</span>',
            'high' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Tinggi</span>'
        ];
        
        return $badges[strtolower($priority)] ?? $badges['medium'];
    }
}

if (!function_exists('can_edit_status')) {
    /**
     * Check if status can be edited
     * 
     * @param string $status
     * @return bool
     */
    function can_edit_status($status)
    {
        $editableStatuses = ['draft'];
        
        return in_array(strtolower($status), $editableStatuses);
    }
}

if (!function_exists('can_delete_status')) {
    /**
     * Check if record with status can be deleted
     * 
     * @param string $status
     * @return bool
     */
    function can_delete_status($status)
    {
        $deletableStatuses = ['draft', 'rejected'];
        
        return in_array(strtolower($status), $deletableStatuses);
    }
}

if (!function_exists('get_next_status')) {
    /**
     * Get next status in workflow
     * 
     * @param string $currentStatus
     * @param string $type (program|kegiatan|sppd)
     * @return string|null
     */
    function get_next_status($currentStatus, $type = 'sppd')
    {
        $workflow = [
            'program' => [
                'draft' => 'pending',
                'pending' => 'approved',
                'rejected' => null
            ],
            'sppd' => [
                'draft' => 'pending',
                'pending' => 'approved',
                'approved' => 'submitted',
                'submitted' => 'verified',
                'need_revision' => 'submitted',
                'verified' => 'closed'
            ]
        ];
        
        return $workflow[$type][$currentStatus] ?? null;
    }
}