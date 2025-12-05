<?php

// ========================================
// VALIDATION CONTROLLER
// ========================================

namespace App\Controllers\API;

use App\Controllers\BaseController;

class ValidationController extends BaseController
{
    /**
     * Check if NIP/NIK is unique
     */
    public function checkNipNik()
    {
        $nipNik = $this->request->getPost('nip_nik');
        $userId = $this->request->getPost('user_id');

        $userModel = new \App\Models\User\UserModel();
        $builder = $userModel->where('nip_nik', $nipNik);

        if ($userId) {
            $builder->where('id !=', $userId);
        }

        $exists = $builder->countAllResults() > 0;

        return $this->respond(['valid' => !$exists]);
    }

    /**
     * Check if email is unique
     */
    public function checkEmail()
    {
        $email = $this->request->getPost('email');
        $userId = $this->request->getPost('user_id');

        $userModel = new \App\Models\User\UserModel();
        $builder = $userModel->where('email', $email);

        if ($userId) {
            $builder->where('id !=', $userId);
        }

        $exists = $builder->countAllResults() > 0;

        return $this->respond(['valid' => !$exists]);
    }

    /**
     * Check if kode program is unique
     */
    public function checkKodeProgram()
    {
        $kode = $this->request->getPost('kode_program');
        $programId = $this->request->getPost('program_id');

        $programModel = new \App\Models\Program\ProgramModel();
        $builder = $programModel->where('kode_program', $kode);

        if ($programId) {
            $builder->where('id !=', $programId);
        }

        $exists = $builder->countAllResults() > 0;

        return $this->respond(['valid' => !$exists]);
    }

    /**
     * Check if No SPPD is unique
     */
    public function checkNoSppd()
    {
        $noSppd = $this->request->getPost('no_sppd');
        $sppdId = $this->request->getPost('sppd_id');

        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $builder = $sppdModel->where('no_sppd', $noSppd);

        if ($sppdId) {
            $builder->where('id !=', $sppdId);
        }

        $exists = $builder->countAllResults() > 0;

        return $this->respond(['valid' => !$exists]);
    }
}