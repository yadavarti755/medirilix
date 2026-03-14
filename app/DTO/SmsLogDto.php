<?php

namespace App\DTO;

class SmsLogDto
{
    public string $recipient_sms;
    public ?string $recipient_name;
    public string $sms_type;
    public string $subject;
    public string $status;
    public ?string $error_message;
    public ?array $metadata;
    public string $sent_at;

    public function __construct(
        string $recipient_sms,
        string $sms_type,
        string $subject,
        string $status = 'failed',
        ?string $recipient_name = null,
        ?string $error_message = null,
        ?array $metadata = null,
        $sent_at = null
    ) {
        $this->recipient_sms = $recipient_sms;
        $this->recipient_name = $recipient_name;
        $this->sms_type = $sms_type;
        $this->subject = $subject;
        $this->status = $status;
        $this->error_message = $error_message;
        $this->metadata = $metadata;
        $this->sent_at = $sent_at;
    }
}
