<?php

namespace App\Traits;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    public function logActivity($action, $description = null, $model = null)
    {
        UserActivity::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}

?>