<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experiment extends Model
{
    protected $guarded = [];

    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'next_paths' => 'array',
        'macro_shift' => 'boolean',
        'year' => 'integer',
    ];

    public function age(): BelongsTo
    {
        return $this->belongsTo(Age::class, 'age_id', 'id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }

    /**
     * Connections originating from this experiment to earlier targets (Ancestors / Roots).
     */
    public function outgoingConnections(): HasMany
    {
        return $this->hasMany(Connection::class, 'source_experiment_id', 'id');
    }

    /**
     * Connections pointing to this experiment from later sources (Descendants / Fruits).
     */
    public function incomingConnections(): HasMany
    {
        return $this->hasMany(Connection::class, 'target_experiment_id', 'id');
    }

    /**
     * Name helper (prefers person model, fallback to override).
     */
    public function getPersonDisplayNameAttribute(): string
    {
        return $this->person_name_override ?: ($this->person ? $this->person->name : 'Unknown Thinker');
    }
}
