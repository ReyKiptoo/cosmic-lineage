<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Connection extends Model
{
    protected $guarded = [];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Experiment::class, 'source_experiment_id', 'id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(Experiment::class, 'target_experiment_id', 'id');
    }
}
