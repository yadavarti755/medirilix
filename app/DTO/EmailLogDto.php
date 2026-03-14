<?php

namespace App\DTO;

class EmailLogDto
{
    public string $recipient_email;
    public ?string $recipient_name;
    public string $email_type;
    public string $subject;
    public string $status;
    public ?string $error_message;
    public ?array $metadata;
    public string $sent_at;

    public function __construct(
        string $recipient_email,
        string $email_type,
        string $subject,
        string $status = 'failed',
        ?string $recipient_name = null,
        ?string $error_message = null,
        ?array $metadata = null,
        $sent_at = null
    ) {
        $this->recipient_email = $recipient_email;
        $this->recipient_name = $recipient_name;
        $this->email_type = $email_type;
        $this->subject = $subject;
        $this->status = $status;
        $this->error_message = $error_message;
        $this->metadata = $metadata;
        $this->sent_at = $sent_at;
    }
}
