<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

#[ApiResource]
class Progression extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'article_id', 'termine', 'termine_at'];

    protected $casts = [
        'termine' => 'boolean',
        'termine_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
