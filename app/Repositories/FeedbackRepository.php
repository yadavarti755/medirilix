<?php

namespace App\Repositories;

use App\Models\Feedback;

class FeedbackRepository
{
    public function findForPublic()
    {
        return Feedback::orderBy('id', 'DESC')->get();
    }

    public function findAll()
    {
        return Feedback::orderBy('id', 'DESC');
    }

    public function findById($id)
    {
        return Feedback::find($id);
    }

    public function create($data)
    {
        return Feedback::create($data);
    }

    public function update($data, $id)
    {
        $result = Feedback::find($id);
        if ($result) {
            $result = $result->update($data);
            if (!$result) {
                return false;
            }
            return $result;
        }
        return false;
    }

    public function delete($id)
    {
        $result = Feedback::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
