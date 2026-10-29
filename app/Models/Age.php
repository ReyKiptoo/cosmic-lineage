<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Age extends Model
{
    protected $guarded = [];

    public $incrementing = false;

    public function experiments(): HasMany
    {
        return $this->hasMany(Experiment::class, 'age_id', 'id')->orderBy('year');
    }
}
