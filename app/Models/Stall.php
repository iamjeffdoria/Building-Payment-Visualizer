<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stall extends Model
{
    public const STATUS_BADGES = [
        'paid' => 'bg-green-100 text-green-800',
        'unpaid' => 'bg-gray-200 text-gray-700',
        'overdue' => 'bg-red-100 text-red-800',
    ];

    public const MONTHS = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    // "amount" is the MONTHLY amount due.
    protected $fillable = ['building_id', 'stall_no', 'tenant_name', 'amount'];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    // The ledger: one row per paid month.
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('year')->orderBy('month');
    }

    // Status for the CURRENT year, up to the current month.
    // overdue: a month before this one is unpaid
    // unpaid:  only this month is unpaid
    // paid:    everything up to and including this month is paid
    public function getStatusAttribute(): string
    {
        $year = today()->year;
        $month = today()->month;

        $paid = $this->payments->where('year', $year)->pluck('month');
        $pastPaid = $paid->filter(fn ($m) => $m < $month)->count();

        if ($pastPaid < $month - 1) {
            return 'overdue';
        }

        return $paid->contains($month) ? 'paid' : 'unpaid';
    }

    // Same rules as getStatusAttribute, but as a database filter.
    public function scopeOfStatus(Builder $query, ?string $status): Builder
    {
        $year = today()->year;
        $month = today()->month;

        $past = '(select count(*) from payments where payments.stall_id = stalls.id and payments.year = ? and payments.month < ?)';
        $current = '(select count(*) from payments where payments.stall_id = stalls.id and payments.year = ? and payments.month = ?)';

        return match ($status) {
            'overdue' => $query->whereRaw("$past < ?", [$year, $month, $month - 1]),
            'paid' => $query->whereRaw("$past >= ?", [$year, $month, $month - 1])
                ->whereRaw("$current > 0", [$year, $month]),
            'unpaid' => $query->whereRaw("$past >= ?", [$year, $month, $month - 1])
                ->whereRaw("$current = 0", [$year, $month]),
            default => $query,
        };
    }
}