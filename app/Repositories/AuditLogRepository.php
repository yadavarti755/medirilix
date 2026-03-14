<?php

namespace App\Repositories;

use Spatie\Activitylog\Models\Activity;

class AuditLogRepository
{
    public function findAllForDatatable()
    {
        return Activity::latest();
    }

    public function findAll()
    {
        return Activity::latest()->get();
    }

    public function findById($id)
    {
        return Activity::find($id);
    }

    public function findByLogName(string $logName)
    {
        return Activity::where('log_name', $logName)->latest()->get();
    }

    public function findByCauser($userId)
    {
        return Activity::where('causer_id', $userId)->latest()->get();
    }

    public function delete($id)
    {
        $activity = Activity::find($id);
        if ($activity) {
            return $activity->delete();
        }
        return false;
    }
}
