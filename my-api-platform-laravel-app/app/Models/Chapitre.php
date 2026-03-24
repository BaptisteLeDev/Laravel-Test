<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[ApiResource]
class Chapitre extends Model
{
    use HasFactory;

    protected $fillable = ['titre', 'ordre', 'formation_id'];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
