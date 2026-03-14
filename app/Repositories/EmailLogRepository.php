<?php

namespace App\Repositories;

use App\Models\EmailLog;

class EmailLogRepository
{
    public function findForPublicWithPagination($perPage = 10)
    {
        return EmailLog::orderBy('id', 'DESC')->paginate($perPage);
    }

    public function findForPublic()
    {
        return EmailLog::orderBy('id', 'DESC')->get();
    }

    public function findAll()
    {
        return EmailLog::orderBy('id', 'DESC')->get();
    }

    public function findById($id)
    {
        return EmailLog::find($id);
    }

    public function create($data)
    {
        return EmailLog::create($data);
    }

    public function update($data, $id)
    {
        $result = EmailLog::find($id);
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
        $result = EmailLog::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function search(array $fields, string $term)
    {
        return EmailLog::where(function ($query) use ($fields, $term) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%' . $term . '%');
            }
        })
            ->orderBy('id', 'desc')
            ->get();
    }
}
