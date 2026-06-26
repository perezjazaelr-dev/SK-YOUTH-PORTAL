<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Initiative extends Model
{
    use HasFactory;

    protected $fillable = ['committee_id', 'title', 'description', 'form_route', 'custom_fields'];

    protected function casts(): array
    {
        return [
            'custom_fields' => 'array',
        ];
    }

    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class);
    }

    public function accomplishmentReports(): HasMany
    {
        return $this->hasMany(AccomplishmentReport::class);
    }
}
