<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentoringSession extends Model
{
    use HasFactory;

    protected $table = 'mentoring_sessions';

    protected $fillable = [
        'user_id',
        'mentor_id',
        'topic',
        'date',
        'time',
        'duration',
        'type',
        'status',
        'rating',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date'   => 'date',
            'rating' => 'integer',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
