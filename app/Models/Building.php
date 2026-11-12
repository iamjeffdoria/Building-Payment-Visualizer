<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = ['code', 'name', 'short', 'rent'];

    public function stalls(): HasMany
    {
        return $this->hasMany(Stall::class)->orderBy('stall_no');
    }
}