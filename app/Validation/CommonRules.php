<?php

namespace App\Validation;

/**
 * Small reusable validation helpers
 */
class CommonRules
{
    /**
     * Checks that a numeric field is positive (greater than or equal to zero)
     */
    public function is_positive_number(string $str, string &$error = null): bool
    {
        if (!is_numeric($str)) {
            $error = 'Nilai harus berupa angka';
            return false;
        }

        if (floatval($str) < 0) {
            $error = 'Nilai tidak boleh negatif';
            return false;
        }

        return true;
    }

    /**
     * Simple currency format check (allows numbers and optional decimals)
     */
    public function valid_currency(string $str, string &$error = null): bool
    {
        if ($str === null || $str === '') {
            return true;
        }

        if (!preg_match('/^-?\d+(?:\.\d+)?$/', $str)) {
            $error = 'Format mata uang tidak valid';
            return false;
        }

        return true;
    }
}
