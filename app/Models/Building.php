<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = ['code', 'name', 'short', 'rent', 'sort'];

    public function stalls(): HasMany
    {
        return $this->hasMany(Stall::class);
    }

    // Adds: stalls_count, paid_count (fully paid), collected, expected
    public function scopeWithStallStats(Builder $query): Builder
    {
        return $query
            ->withCount([
                'stalls',
                'stalls as paid_count' => fn ($q) => $q->whereColumn('amount_paid', '>=', 'amount'),
            ])
            ->withSum('stalls as collected', 'amount_paid')
            ->withSum('stalls as expected', 'amount');
    }
}