<?php

namespace App\Validation;

/**
 * Validation rules related to Kwitansi operations
 */
class KwitansiRules
{
    /**
     * Validate that total biaya does not exceed SPPD estimasi
     */
    public function total_not_exceed_estimasi(string $str, string $params, array $data, string &$error = null): bool
    {
        $totalBiaya = floatval($str);
        $sppdId = $data['sppd_id'] ?? null;

        if (!$sppdId) {
            return true;
        }

        $db = \Config\Database::connect();
        $sppd = $db->table('sppd')->where('id', $sppdId)->get()->getRow();

        if (!$sppd) {
            $error = 'SPPD tidak ditemukan';
            return false;
        }

        if ($totalBiaya > $sppd->estimasi_biaya) {
            $error = 'Total biaya melebihi estimasi biaya SPPD: Rp ' . number_format($sppd->estimasi_biaya, 0, ',', '.');
            return false;
        }

        return true;
    }
}
