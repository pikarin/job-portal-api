<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $hire_manager_id
 * @property string $status
 * @property string $title
 * @property string|null $description
 * @property string|null $complexity
 * @property string|null $duration
 * @property string|null $payment_amount
 * @property string|null $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property \App\Models\HireManager $hire_manager
 * @property \Illuminate\Database\Eloquent\Collection<int,\App\Models\Skill> $skills
 * @property \Illuminate\Database\Eloquent\Collection<int,\App\Models\Proposal> $proposals
 */
class Job extends Model
{
    use SoftDeletes;

    public function hireManager(): BelongsTo
    {
        return $this->belongsTo(HireManager::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}
