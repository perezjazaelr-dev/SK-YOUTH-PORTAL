<?php

namespace App\Models;

use App\Traits\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportsRegistration extends Model
{
    use HasComments, HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'age',
        'gender',
        'email',
        'contact_number',
        'sport',
        'division',
        'team_name',
        'event_date',
        'remarks',
        'status',
        'custom_fields',
        'processed_by',
    ];

    /**
     * Relationship: A request has a user who processed it.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'age' => 'integer',
            'custom_fields' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::created(function ($model) {
            app(\App\Services\MailDispatchService::class)->queueRequestReceived($model);
        });
    }
}
