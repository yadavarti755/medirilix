<?php

namespace App\Repositories;

use App\Models\VerificationCode;
use Carbon\Carbon;

class VerificationCodeRepository
{

    /**
     * Create a new verification code
     */
    public function create(array $data)
    {
        return VerificationCode::create($data);
    }

    /**
     * Find verification code by email and code
     */
    public function findByEmailAndCode($email, $code)
    {
        return VerificationCode::where([
            'email_id' => $email,
            'code' => $code
        ])->first();
    }

    /**
     * Mark all OTPs as used for an email
     */
    public function markAllAsUsed($email)
    {
        return VerificationCode::where('email_id', $email)
            ->update(['otp_used' => 1]);
    }

    /**
     * Get latest unused verification code
     */
    public function getLatestUnusedCode($email)
    {
        return VerificationCode::where('email_id', $email)
            ->where('otp_used', 0)
            ->where('expiration_time', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Delete old verification codes
     */
    public function deleteExpiredCodes($email)
    {
        return VerificationCode::where('email_id', $email)
            ->where('expiration_time', '<', Carbon::now())
            ->delete();
    }
}
