<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'excerpt',
        'category',
        'likes',
        'replies',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'likes'     => 'integer',
            'replies'   => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
