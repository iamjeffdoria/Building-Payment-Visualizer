<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['stall_id', 'amount', 'paid_on', 'note'];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'paid_on' => 'date:Y-m-d',
        ];
    }

    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }
}