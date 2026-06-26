<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name', 'slug'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function initiatives(): HasMany
    {
        return $this->hasMany(Initiative::class);
    }
}
