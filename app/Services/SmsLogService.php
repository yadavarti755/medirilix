<?php

namespace App\Services;

use App\DTO\SmsLogDto;
use App\Repositories\SmsLogRepository;

class SmsLogService
{
    private $smsLogRepository;

    public function __construct()
    {
        $this->smsLogRepository = new SmsLogRepository();
    }

    public function findForPublic()
    {
        return $this->smsLogRepository->findForPublic();
    }

    public function findAll()
    {
        return $this->smsLogRepository->findAll();
    }

    public function findById($id)
    {
        return $this->smsLogRepository->findById($id);
    }

    public function create(SmsLogDto $smsLogDto)
    {

        $result = $this->smsLogRepository->create([
            'recipient_sms' => $smsLogDto->recipient_sms,
            'recipient_name' => $smsLogDto->recipient_name,
            'sms_type' => $smsLogDto->sms_type,
            'subject' => $smsLogDto->subject,
            'status' => $smsLogDto->status,
            'error_message' => $smsLogDto->error_message,
            'metadata' => $smsLogDto->metadata,
            'sent_at' => $smsLogDto->sent_at,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function delete($id)
    {
        return $this->smsLogRepository->delete($id);
    }
}
