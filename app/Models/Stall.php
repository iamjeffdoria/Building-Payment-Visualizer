<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stall extends Model
{
    protected $fillable = ['building_id', 'stall_no', 'tenant_name', 'amount', 'status'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}