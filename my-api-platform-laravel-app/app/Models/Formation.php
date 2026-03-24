<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[ApiResource]
class Formation extends Model
{
    use HasFactory;

    protected $fillable = ['titre', 'description', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapitres(): HasMany
    {
        return $this->hasMany(Chapitre::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class)->withTimestamps();
    }
}
