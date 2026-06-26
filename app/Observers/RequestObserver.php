<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class RequestObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        ActivityLog::record('request_created', $model, [
            'type' => class_basename($model)
        ], null);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        if ($model->isDirty('status')) {
            ActivityLog::record('status_changed', $model, [
                'from' => $model->getOriginal('status'),
                'to' => $model->status,
            ], auth()->id());
        } else {
            $changes = [];
            foreach ($model->getDirty() as $key => $value) {
                $changes[$key] = [
                    'from' => $model->getOriginal($key),
                    'to' => $value
                ];
            }
            ActivityLog::record('request_updated', $model, [
                'type' => class_basename($model),
                'changes' => $changes
            ], auth()->id());
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        ActivityLog::record('request_cancelled', $model, [
            'type' => class_basename($model),
            'email' => $model->email
        ], auth()->id());
    }
}
