<?php

/**
 * Format Helper
 * 
 * Helper functions for formatting data
 */

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Rupiah currency
     * 
     * @param float|int $amount
     * @param bool $withPrefix
     * @return string
     */
    function format_rupiah($amount, $withPrefix = true)
    {
        $formatted = number_format($amount, 0, ',', '.');
        
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('format_number')) {
    /**
     * Format number with thousands separator
     * 
     * @param float|int $number
     * @param int $decimals
     * @return string
     */
    function format_number($number, $decimals = 0)
    {
        return number_format($number, $decimals, ',', '.');
    }
}

if (!function_exists('format_tanggal')) {
    /**
     * Format date to Indonesian format
     * 
     * @param string $date
     * @param bool $withDay
     * @return string
     */
    function format_tanggal($date, $withDay = true)
    {
        if (!$date || $date == '0000-00-00') {
            return '-';
        }
        
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        
        $timestamp = strtotime($date);
        $day = date('j', $timestamp);
        $month = $bulan[date('n', $timestamp)];
        $year = date('Y', $timestamp);
        $dayName = $hari[date('l', $timestamp)];
        
        if ($withDay) {
            return $dayName . ', ' . $day . ' ' . $month . ' ' . $year;
        }
        
        return $day . ' ' . $month . ' ' . $year;
    }
}

if (!function_exists('format_tanggal_waktu')) {
    /**
     * Format datetime to Indonesian format
     * 
     * @param string $datetime
     * @return string
     */
    function format_tanggal_waktu($datetime)
    {
        if (!$datetime || $datetime == '0000-00-00 00:00:00') {
            return '-';
        }
        
        return format_tanggal($datetime) . ' ' . date('H:i', strtotime($datetime)) . ' WIB';
    }
}

if (!function_exists('format_waktu')) {
    /**
     * Format time
     * 
     * @param string $time
     * @return string
     */
    function format_waktu($time)
    {
        if (!$time) {
            return '-';
        }
        
        return date('H:i', strtotime($time)) . ' WIB';
    }
}

if (!function_exists('format_nip')) {
    /**
     * Format NIP with space separator
     * 
     * @param string $nip
     * @return string
     */
    function format_nip($nip)
    {
        if (strlen($nip) == 18) {
            // Format: 19900101 202001 1 001
            return substr($nip, 0, 8) . ' ' . substr($nip, 8, 6) . ' ' . substr($nip, 14, 1) . ' ' . substr($nip, 15, 3);
        }
        
        return $nip;
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format file size to human readable
     * 
     * @param int $bytes
     * @return string
     */
    function format_file_size($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        
        return $bytes;
    }
}

if (!function_exists('format_phone')) {
    /**
     * Format phone number
     * 
     * @param string $phone
     * @return string
     */
    function format_phone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Format: 0812-3456-7890
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
        }
        
        return $phone;
    }
}

if (!function_exists('time_ago')) {
    /**
     * Convert datetime to "time ago" format
     * 
     * @param string $datetime
     * @return string
     */
    function time_ago($datetime)
    {
        if (!$datetime) {
            return '-';
        }
        
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' menit yang lalu';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' jam yang lalu';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' hari yang lalu';
        } elseif ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return $weeks . ' minggu yang lalu';
        } elseif ($diff < 31536000) {
            $months = floor($diff / 2592000);
            return $months . ' bulan yang lalu';
        } else {
            $years = floor($diff / 31536000);
            return $years . ' tahun yang lalu';
        }
    }
}

if (!function_exists('format_percentage')) {
    /**
     * Format number to percentage
     * 
     * @param float $number
     * @param int $decimals
     * @return string
     */
    function format_percentage($number, $decimals = 2)
    {
        return number_format($number, $decimals, ',', '.') . '%';
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text to specific length
     * 
     * @param string $text
     * @param int $length
     * @param string $suffix
     * @return string
     */
    function truncate_text($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('format_kode_program')) {
    /**
     * Format program code
     * 
     * @param string $kode
     * @return string
     */
    function format_kode_program($kode)
    {
        return strtoupper($kode);
    }
}

if (!function_exists('parse_rupiah')) {
    /**
     * Parse rupiah string to number
     * 
     * @param string $rupiah
     * @return float
     */
    function parse_rupiah($rupiah)
    {
        // Remove Rp, spaces, and dots
        $number = str_replace(['Rp', ' ', '.'], '', $rupiah);
        
        // Replace comma with dot for decimal
        $number = str_replace(',', '.', $number);
        
        return (float) $number;
    }
}

if (!function_exists('format_period')) {
    /**
     * Format period from date range
     * 
     * @param string $startDate
     * @param string $endDate
     * @return string
     */
    function format_period($startDate, $endDate)
    {
        return format_tanggal($startDate, false) . ' - ' . format_tanggal($endDate, false);
    }
}

if (!function_exists('countdown_days')) {
    /**
     * Calculate countdown days from now
     * 
     * @param string $date
     * @return int
     */
    function countdown_days($date)
    {
        $targetDate = strtotime($date);
        $now = strtotime(date('Y-m-d'));
        $diff = $targetDate - $now;
        
        return floor($diff / 86400);
    }
}

if (!function_exists('calculate_lama_perjalanan')) {
    /**
     * Calculate trip duration in days
     * 
     * @param string $tanggalBerangkat
     * @param string $tanggalKembali
     * @return int
     */
    function calculate_lama_perjalanan($tanggalBerangkat, $tanggalKembali)
    {
        $start = strtotime($tanggalBerangkat);
        $end = strtotime($tanggalKembali);
        $diff = $end - $start;
        
        return floor($diff / 86400) + 1; // +1 to include both start and end day
    }
}

if (!function_exists('format_alamat')) {
    /**
     * Format address
     * 
     * @param string $alamat
     * @return string
     */
    function format_alamat($alamat)
    {
        return ucwords(strtolower($alamat));
    }
}

if (!function_exists('format_jabatan')) {
    /**
     * Format job title
     * 
     * @param string $jabatan
     * @return string
     */
    function format_jabatan($jabatan)
    {
        return ucwords(strtolower($jabatan));
    }
}

if (!function_exists('highlight_search')) {
    /**
     * Highlight search keywords in text
     * 
     * @param string $text
     * @param string $keyword
     * @return string
     */
    function highlight_search($text, $keyword)
    {
        if (!$keyword) {
            return $text;
        }
        
        return preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<mark class="bg-yellow-200">$1</mark>', $text);
    }
}

if (!function_exists('bulan_romawi')) {
    /**
     * Convert month number to roman numeral
     * 
     * @param int $month
     * @return string
     */
    function bulan_romawi($month)
    {
        $romawi = [
            1 => 'I', 'II', 'III', 'IV', 'V', 'VI',
            'VII', 'VIII', 'IX', 'X', 'XI', 'XII'
        ];
        
        return $romawi[$month] ?? '';
    }
}

if (!function_exists('sanitize_filename')) {
    /**
     * Sanitize filename for safe storage
     * 
     * @param string $filename
     * @return string
     */
    function sanitize_filename($filename)
    {
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);
        
        return strtolower($filename);
    }
}