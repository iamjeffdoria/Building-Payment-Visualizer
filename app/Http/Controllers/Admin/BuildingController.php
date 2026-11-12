<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::query()
            ->withCount([
                'stalls',
                'stalls as paid_count' => fn ($q) => $q->where('status', 'paid'),
            ])
            ->orderBy('name')
            ->get();

        return view('admin.buildings.index', compact('buildings'));
    }

    public function show(Building $building)
    {
        $building->load('stalls');

        return view('admin.buildings.show', compact('building'));
    }
}