<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $name
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Freelancer> $freelancers
 */
class Skill extends Model
{
    public $timestamps = false;

    public function freelancers(): BelongsToMany
    {
        return $this->belongsToMany(Freelancer::class);
    }
}
