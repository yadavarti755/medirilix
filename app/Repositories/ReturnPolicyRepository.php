<?php

namespace App\Repositories;

use App\Models\ReturnPolicy;
use Illuminate\Support\Facades\Log;

class ReturnPolicyRepository
{
    public function create(array $data)
    {
        try {
            return ReturnPolicy::create($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function update(array $data, $id)
    {
        try {
            $policy = ReturnPolicy::find($id);
            if ($policy) {
                $policy->update($data);
                return $policy;
            }
            return false;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function find($id)
    {
        return ReturnPolicy::find($id);
    }

    public function all()
    {
        return ReturnPolicy::all();
    }

    public function delete($id)
    {
        try {
            $policy = ReturnPolicy::find($id);
            if ($policy) {
                $policy->delete(); // Soft delete
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function findForDatatable($where = [])
    {
        return ReturnPolicy::select([
            'id',
            'title',
            'return_till_days',
            'return_description',
            'created_by',
            'updated_by',
            'created_at'
        ])
            ->with(['creator', 'editor'])
            ->get();
    }
}
