<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[ApiResource]
class Article extends Model
{
    use HasFactory;

    protected $fillable = ['titre', 'contenu', 'ordre', 'chapitre_id'];

    public function chapitre(): BelongsTo
    {
        return $this->belongsTo(Chapitre::class);
    }

    public function progressions(): HasMany
    {
        return $this->hasMany(Progression::class);
    }
}
