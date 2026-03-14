<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_sms',
        'recipient_name',
        'sms_type',
        'subject',
        'status',
        'error_message',
        'metadata',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Email types constants
    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_BACKEND_OTP = 'backend_otp';
    const TYPE_EMPLOYEE_OTP = 'employee_otp';
    const TYPE_APPLICATION_CONFIRMATION = 'dhti_application_confirmation';
    const TYPE_CERTIFICATE_ISSUED = 'certificate_issued';

    // Status constants
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    /**
     * Scope to filter by sms type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('sms_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by recipient
     */
    public function scopeByRecipient($query, string $sms)
    {
        return $query->where('recipient_sms', $sms);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, Carbon $from, Carbon $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Get successful smss
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Get failed smss
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
