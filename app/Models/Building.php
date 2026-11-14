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

    // Adds: stalls_count, paid_count (stalls up to date this month), monthly (sum of monthly rents),
    // collected (paid this year, Jan up to the current month).
    // "expected" is computed from monthly (see getExpectedAttribute).
    public function scopeWithStallStats(Builder $query): Builder
    {
        $year = today()->year;
        $month = today()->month;

        return $query
            ->withCount([
                'stalls',
                'stalls as paid_count' => fn ($q) => $q->ofStatus('paid'),
            ])
            ->withSum('stalls as monthly', 'amount')
            ->addSelect(['collected' => Payment::query()
                ->selectRaw('coalesce(sum(payments.amount), 0)')
                ->join('stalls', 'stalls.id', '=', 'payments.stall_id')
                ->whereColumn('stalls.building_id', 'buildings.id')
                ->where('payments.year', $year)
                ->where('payments.month', '<=', $month)]);
    }

    // What should have been collected from January up to the current month.
    public function getExpectedAttribute(): float
    {
        return (float) $this->monthly * today()->month;
    }
}