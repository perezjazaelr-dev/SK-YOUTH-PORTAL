<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requestor_first_name',
        'requestor_last_name',
        'requestor_age',
        'requestor_gender',
        'email',
        'contact_number',
        'complete_address',
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
            'requestor_age' => 'integer',
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
