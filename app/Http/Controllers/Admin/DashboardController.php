<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $buildingData = Building::with('stalls')->get()->mapWithKeys(fn ($b) => [
            $b->code => [
                'id' => $b->id,
                'url' => route('admin.buildings.show', $b),
                'stalls' => $b->stalls->map(fn ($s) => [
                    'no' => $s->stall_no,
                    'tenant' => $s->tenant_name,
                    'amount' => (float) $s->amount,
                    'status' => $s->status,
                ])->values(),
            ],
        ]);

        return view('admin.dashboard', [
            'user' => $request->user(),
            'buildingData' => $buildingData,
        ]);
    }
}