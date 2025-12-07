<?php

namespace App\Libraries\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Service
 * 
 * Handles email sending using PHPMailer
 * Requires: composer require phpmailer/phpmailer
 */
class EmailService
{
    protected $mailer;
    protected $config;

    public function __construct()
    {
        $this->config = config('Email');
        $this->initializeMailer();
    }

    /**
     * Initialize PHPMailer
     */
    protected function initializeMailer()
    {
        $this->mailer = new PHPMailer(true);

        try {
            // Server settings
            $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;
            $this->mailer->isSMTP();
            $this->mailer->Host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = getenv('SMTP_USER');
            $this->mailer->Password   = getenv('SMTP_PASS');
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = getenv('SMTP_PORT') ?: 587;
            $this->mailer->CharSet    = 'UTF-8';

            // Default from address
            $this->mailer->setFrom(
                getenv('SMTP_FROM') ?: 'noreply@perjadin.com',
                'Aplikasi Perjadin'
            );
        } catch (Exception $e) {
            log_message('error', 'Email initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Send email notification
     */
    public function send($to, $subject, $message, $attachments = [])
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Recipients
            if (is_array($to)) {
                foreach ($to as $email => $name) {
                    if (is_numeric($email)) {
                        $this->mailer->addAddress($name);
                    } else {
                        $this->mailer->addAddress($email, $name);
                    }
                }
            } else {
                $this->mailer->addAddress($to);
            }

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $message;
            $this->mailer->AltBody = strip_tags($message);

            // Attachments
            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    if (is_array($file)) {
                        $this->mailer->addAttachment($file['path'], $file['name'] ?? '');
                    } else {
                        $this->mailer->addAttachment($file);
                    }
                }
            }

            $this->mailer->send();
            return true;

        } catch (Exception $e) {
            log_message('error', "Email sending failed: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    /**
     * Send notification when program is approved
     */
    public function sendProgramApproved($userEmail, $userName, $programName)
    {
        $subject = 'Program Disetujui - Aplikasi Perjadin';
        $message = view('emails/program_approved', [
            'name' => $userName,
            'program_name' => $programName,
        ]);

        return $this->send($userEmail, $subject, $message);
    }

    /**
     * Send notification when program is rejected
     */
    public function sendProgramRejected($userEmail, $userName, $programName, $reason)
    {
        $subject = 'Program Ditolak - Aplikasi Perjadin';
        $message = view('emails/program_rejected', [
            'name' => $userName,
            'program_name' => $programName,
            'reason' => $reason,
        ]);

        return $this->send($userEmail, $subject, $message);
    }

    /**
     * Send notification when SPPD is approved
     */
    public function sendSppdApproved($userEmail, $userName, $noSppd, $pdfPath = null)
    {
        $subject = 'SPPD Disetujui - Aplikasi Perjadin';
        $message = view('emails/sppd_approved', [
            'name' => $userName,
            'no_sppd' => $noSppd,
        ]);

        $attachments = [];
        if ($pdfPath && file_exists($pdfPath)) {
            $attachments[] = [
                'path' => $pdfPath,
                'name' => 'Nota_Dinas_' . str_replace('/', '_', $noSppd) . '.pdf'
            ];
        }

        return $this->send($userEmail, $subject, $message, $attachments);
    }

    /**
     * Send notification when SPPD needs verification
     */
    public function sendSppdSubmitted($keuanganEmails, $noSppd, $bidangName)
    {
        $subject = 'SPPD Perlu Verifikasi - Aplikasi Perjadin';
        $message = view('emails/sppd_submitted', [
            'no_sppd' => $noSppd,
            'bidang_name' => $bidangName,
        ]);

        return $this->send($keuanganEmails, $subject, $message);
    }

    /**
     * Send notification when SPPD is verified
     */
    public function sendSppdVerified($userEmail, $userName, $noSppd)
    {
        $subject = 'SPPD Terverifikasi - Aplikasi Perjadin';
        $message = view('emails/sppd_verified', [
            'name' => $userName,
            'no_sppd' => $noSppd,
        ]);

        return $this->send($userEmail, $subject, $message);
    }

    /**
     * Send notification when SPPD needs revision
     */
    public function sendSppdNeedRevision($userEmail, $userName, $noSppd, $reason)
    {
        $subject = 'SPPD Perlu Revisi - Aplikasi Perjadin';
        $message = view('emails/sppd_need_revision', [
            'name' => $userName,
            'no_sppd' => $noSppd,
            'reason' => $reason,
        ]);

        return $this->send($userEmail, $subject, $message);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset($userEmail, $userName, $resetLink)
    {
        $subject = 'Reset Password - Aplikasi Perjadin';
        $message = view('emails/reset_password', [
            'name' => $userName,
            'reset_link' => $resetLink,
            'expiry' => '1 jam',
        ]);

        return $this->send($userEmail, $subject, $message);
    }

    /**
     * Send reminder to fill LPPD
     */
    public function sendLppdReminder($userEmail, $userName, $noSppd)
    {
        $subject = 'Reminder: Isi LPPD - Aplikasi Perjadin';
        $message = view('emails/lppd_reminder', [
            'name' => $userName,
            'no_sppd' => $noSppd,
        ]);

        return $this->send($userEmail, $subject, $message);
    }

    /**
     * Send welcome email for new user
     */
    public function sendWelcomeEmail($userEmail, $userName, $tempPassword)
    {
        $subject = 'Selamat Datang - Aplikasi Perjadin';
        $message = view('emails/welcome', [
            'name' => $userName,
            'email' => $userEmail,
            'temp_password' => $tempPassword,
            'login_url' => base_url('login'),
        ]);

        return $this->send($userEmail, $subject, $message);
    }

    /**
     * Send test email
     */
    public function sendTestEmail($to)
    {
        $subject = 'Test Email - Aplikasi Perjadin';
        $message = '<h1>Test Email</h1><p>Email service is working correctly!</p>';

        return $this->send($to, $subject, $message);
    }

    /**
     * Get last error
     */
    public function getLastError()
    {
        return $this->mailer->ErrorInfo;
    }
}