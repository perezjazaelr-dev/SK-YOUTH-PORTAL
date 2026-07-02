<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_form_id',
        'citizen_name',
        'citizen_email',
        'answers',
        'status',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function registrationForm(): BelongsTo
    {
        return $this->belongsTo(RegistrationForm::class);
    }
}
