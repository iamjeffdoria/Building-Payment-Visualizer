<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $buildingData = Building::query()
            ->withStallStats()
            ->get()
            ->mapWithKeys(fn ($b) => [
                $b->code => [
                    'total' => $b->stalls_count,
                    'rate' => (float) $b->expected > 0 ? (float) $b->collected / (float) $b->expected : 0,
                    'panel' => route('admin.buildings.panel', $b),
                ],
            ]);

        return view('admin.dashboard', [
            'user' => $request->user(),
            'buildingData' => $buildingData,
        ]);
    }
}