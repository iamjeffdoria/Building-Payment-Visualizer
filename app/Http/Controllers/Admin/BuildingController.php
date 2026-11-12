<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

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

    public function show(Request $request, Building $building)
    {
        $building->loadCount('stalls');

        $stalls = $building->stalls()
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->input('search') . '%';
                $q->where(fn ($w) => $w->where('tenant_name', 'like', $term)
                    ->orWhere('stall_no', 'like', $term));
            })
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->input('status')))
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.buildings.partials.stall-results', compact('building', 'stalls'));
        }

        return view('admin.buildings.show', compact('building', 'stalls'));
    }

    // Server-rendered hover panel for the dashboard map
    public function panel(Request $request, Building $building)
    {
        $stalls = $building->stalls()->get();

        $summary = [
            'total' => $stalls->count(),
            'paid' => $stalls->where('status', 'paid')->count(),
            'unpaid' => $stalls->where('status', 'unpaid')->count(),
            'overdue' => $stalls->where('status', 'overdue')->count(),
            'collected' => (float) $stalls->where('status', 'paid')->sum('amount'),
            'expected' => (float) $stalls->sum('amount'),
        ];

        return view('admin.buildings.partials.map-panel', [
            'building' => $building,
            'stalls' => $stalls,
            's' => $summary,
            'pinned' => $request->boolean('pinned'),
        ]);
    }
}