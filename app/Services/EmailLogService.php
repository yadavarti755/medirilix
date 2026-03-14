<?php

namespace App\Services;

use App\DTO\EmailLogDto;
use App\Repositories\EmailLogRepository;

class EmailLogService
{
    private $emailLogRepository;

    public function __construct()
    {
        $this->emailLogRepository = new EmailLogRepository();
    }

    public function findForPublic()
    {
        return $this->emailLogRepository->findForPublic();
    }

    public function findAll()
    {
        return $this->emailLogRepository->findAll();
    }

    public function findById($id)
    {
        return $this->emailLogRepository->findById($id);
    }

    public function create(EmailLogDto $emailLogDto)
    {

        $result = $this->emailLogRepository->create([
            'recipient_email' => $emailLogDto->recipient_email,
            'recipient_name' => $emailLogDto->recipient_name,
            'email_type' => $emailLogDto->email_type,
            'subject' => $emailLogDto->subject,
            'status' => $emailLogDto->status,
            'error_message' => $emailLogDto->error_message,
            'metadata' => $emailLogDto->metadata,
            'sent_at' => $emailLogDto->sent_at,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->emailLogRepository->delete($id);
    }
}
