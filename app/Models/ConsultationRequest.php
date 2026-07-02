<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ConsultationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'category',
        'subject',
        'message',
        'attachment',
        'status',
        'replies',
    ];

    protected $casts = [
        'replies' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $request) {
            if (empty($request->tracking_id)) {
                $request->tracking_id = 'SKO-' . strtoupper(Str::random(8));
            }
        });
    }
}
