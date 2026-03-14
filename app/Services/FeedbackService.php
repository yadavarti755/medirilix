<?php

namespace App\Services;

use App\DTO\FeedbackDto;
use App\Repositories\FeedbackRepository;

class FeedbackService
{
    private $feedbackRepository;

    public function __construct()
    {
        $this->feedbackRepository = new FeedbackRepository();
    }

    public function findForPublic()
    {
        return $this->feedbackRepository->findForPublic();
    }

    public function findAll()
    {
        return $this->feedbackRepository->findAll();
    }

    public function findById($id)
    {
        return $this->feedbackRepository->findById($id);
    }

    public function create(FeedbackDto $feedbackDto)
    {

        $result = $this->feedbackRepository->create([
            'name' => $feedbackDto->name,
            'email' => $feedbackDto->email,
            'mobile_no' => $feedbackDto->mobile_no,
            'message' => $feedbackDto->message,
        ]);

        if (!$result) {
            return false;
        }

        return $result;
    }

    public function update(FeedbackDto $feedbackDto, $id)
    {
        $updateData = [
            'name' => $feedbackDto->name,
            'email' => $feedbackDto->email,
            'mobile_no' => $feedbackDto->mobile_no,
            'message' => $feedbackDto->message,
        ];

        $result = $this->feedbackRepository->update($updateData, $id);

        if (!$result) {
            return false;
        }

        return $result;
    }


    public function delete($id)
    {
        return $this->feedbackRepository->delete($id);
    }
}
