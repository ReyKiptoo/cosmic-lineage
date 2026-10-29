<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $guarded = [];

    protected $keyType = 'string';
    public $incrementing = false;

    public function experiments(): HasMany
    {
        return $this->hasMany(Experiment::class, 'person_id', 'id')->orderBy('year');
    }
}
