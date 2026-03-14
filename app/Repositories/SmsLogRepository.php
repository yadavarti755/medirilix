<?php

namespace App\Repositories;

use App\Models\SmsLog;

class SmsLogRepository
{
    public function findForPublicWithPagination($perPage = 10)
    {
        return SmsLog::orderBy('id', 'DESC')->paginate($perPage);
    }

    public function findForPublic()
    {
        return SmsLog::orderBy('id', 'DESC')->get();
    }

    public function findAll()
    {
        return SmsLog::orderBy('id', 'DESC')->get();
    }

    public function findById($id)
    {
        return SmsLog::find($id);
    }

    public function create($data)
    {
        return SmsLog::create($data);
    }

    public function update($data, $id)
    {
        $result = SmsLog::find($id);
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
        $result = SmsLog::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function search(array $fields, string $term)
    {
        return SmsLog::where(function ($query) use ($fields, $term) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%' . $term . '%');
            }
        })
            ->orderBy('id', 'desc')
            ->get();
    }
}
