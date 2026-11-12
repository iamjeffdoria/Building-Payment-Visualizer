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
            ->withCount([
                'stalls',
                'stalls as paid_count' => fn ($q) => $q->where('status', 'paid'),
            ])
            ->get()
            ->mapWithKeys(fn ($b) => [
                $b->code => [
                    'total' => $b->stalls_count,
                    'paid' => $b->paid_count,
                    'panel' => route('admin.buildings.panel', $b),
                ],
            ]);

        return view('admin.dashboard', [
            'user' => $request->user(),
            'buildingData' => $buildingData,
        ]);
    }
}