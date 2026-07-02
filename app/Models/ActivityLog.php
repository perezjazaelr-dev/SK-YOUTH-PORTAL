<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'payload',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    /**
     * Polymorphic relation to get the parent subject model.
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Relation to get the actor (User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper to write a log record.
     */
    public static function record($action, Model $model, array $payload = null, $userId = null)
    {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'payload' => $payload,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Scope a query to only include records related to sensitive data access (DPO).
     */
    public function scopeDpo($query)
    {
        return $query->where(function ($q) {
            $q->where('action', 'like', '%pii%')
              ->orWhere('payload', 'like', '%pii%');
        });
    }
}
