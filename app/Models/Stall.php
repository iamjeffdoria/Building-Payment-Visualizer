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
        'partial' => 'bg-blue-100 text-blue-800',
        'unpaid' => 'bg-gray-200 text-gray-700',
        'overdue' => 'bg-red-100 text-red-800',
    ];

    protected $fillable = ['building_id', 'stall_no', 'tenant_name', 'amount', 'amount_paid', 'due_date'];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'amount_paid' => 'float',
            'due_date' => 'date:Y-m-d',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('paid_on')->orderByDesc('id');
    }

    public function getBalanceAttribute(): float
    {
        return max(0, round($this->amount - $this->amount_paid, 2));
    }

    public function getPercentPaidAttribute(): int
    {
        if ($this->amount <= 0) {
            return 100;
        }

        return (int) min(100, round($this->amount_paid / $this->amount * 100));
    }

    // paid > overdue > partial > unpaid
    public function getStatusAttribute(): string
    {
        if ($this->balance <= 0) {
            return 'paid';
        }

        if ($this->due_date && $this->due_date->lt(today())) {
            return 'overdue';
        }

        return $this->amount_paid > 0 ? 'partial' : 'unpaid';
    }

    // Same rules as getStatusAttribute, but as a database filter.
    public function scopeOfStatus(Builder $query, ?string $status): Builder
    {
        $today = today()->toDateString();
        $notOverdue = fn ($w) => $w->whereNull('due_date')->orWhere('due_date', '>=', $today);

        return match ($status) {
            'paid' => $query->whereColumn('amount_paid', '>=', 'amount'),
            'overdue' => $query->whereColumn('amount_paid', '<', 'amount')
                ->whereNotNull('due_date')->where('due_date', '<', $today),
            'partial' => $query->whereColumn('amount_paid', '<', 'amount')
                ->where('amount_paid', '>', 0)->where($notOverdue),
            'unpaid' => $query->whereColumn('amount_paid', '<', 'amount')
                ->where('amount_paid', '<=', 0)->where($notOverdue),
            default => $query,
        };
    }

    // Recalculate the cached total from the payment records.
    public function refreshPaid(): void
    {
        $this->forceFill(['amount_paid' => $this->payments()->sum('amount')])->save();
    }
}