<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportsRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'age',
        'gender',
        'email',
        'contact_number',
        'sport',
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
            try {
                \Illuminate\Support\Facades\Mail::to($model->email)->send(new \App\Mail\RequestReceivedMail($model));
            } catch (\Exception $e) {
                // Silently swallow mail exceptions so HTTP transaction succeeds
            }
        });
    }
}
