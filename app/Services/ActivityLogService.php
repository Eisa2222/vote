<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public static function log(string $action, ?Model $model = null, ?string $description = null, array $oldValues = [], array $newValues = []): ActivityLog
    {
        return ActivityLog::log($action, $model, $description, $oldValues, $newValues);
    }
}
