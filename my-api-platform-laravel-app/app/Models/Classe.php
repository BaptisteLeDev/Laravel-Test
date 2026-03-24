<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[ApiResource]
class Classe extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'ecole_id'];

    public function ecole(): BelongsTo
    {
        return $this->belongsTo(Ecole::class);
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function formations(): BelongsToMany
    {
        return $this->belongsToMany(Formation::class)->withTimestamps();
    }
}
