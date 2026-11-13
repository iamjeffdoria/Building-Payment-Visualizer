<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        $num = fn (string $key) => str_replace(',', '', trim((string) $request->input($key)));

        $buildings = Building::query()
            ->withStallStats()
            ->when($request->filled('name'), function ($q) use ($request) {
                $term = '%' . $request->input('name') . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $term)
                    ->orWhere('short', 'like', $term));
            })
            ->orderBy('sort')
            ->get()
            ->when($request->filled('stalls'), fn ($c) => $c->filter(fn ($b) =>
                str_contains((string) $b->stalls_count, $num('stalls'))))
            ->when($request->filled('paid'), fn ($c) => $c->filter(fn ($b) =>
                str_contains((string) $b->paid_count, $num('paid'))))
            ->when($request->filled('collected'), fn ($c) => $c->filter(fn ($b) =>
                str_contains((string) (float) $b->collected, $num('collected'))
                || str_contains((string) (float) $b->expected, $num('collected'))))
            ->when($request->filled('collectible'), fn ($c) => $c->filter(fn ($b) =>
                str_contains((string) max(0, (float) $b->expected - (float) $b->collected), $num('collectible'))))
            ->when($request->filled('rent'), fn ($c) => $c->filter(fn ($b) =>
                str_contains((string) (float) $b->rent, $num('rent'))))
            ->values();

        if ($request->ajax()) {
            return view('admin.buildings.partials.building-results', compact('buildings'));
        }

        return view('admin.buildings.index', compact('buildings'));
    }

    public function show(Request $request, Building $building)
    {
        $building->loadCount('stalls')
            ->loadSum('stalls as collected', 'amount_paid')
            ->loadSum('stalls as expected', 'amount');

        $stalls = $building->stalls()
            ->when($request->filled('stall_no'), fn ($q) =>
                $q->where('stall_no', 'like', '%' . $request->input('stall_no') . '%'))
            ->when($request->filled('tenant'), fn ($q) =>
                $q->where('tenant_name', 'like', '%' . $request->input('tenant') . '%'))
            ->when($request->filled('amount'), function ($q) use ($request) {
                $term = '%' . str_replace(',', '', $request->input('amount')) . '%';
                $q->where(fn ($w) => $w->where('amount', 'like', $term)
                    ->orWhere('amount_paid', 'like', $term));
            })
            ->when($request->filled('due_date'), fn ($q) =>
                $q->whereDate('due_date', $request->input('due_date')))
            ->when($request->filled('status'), fn ($q) => $q->ofStatus($request->input('status')))
            ->orderByRaw('LENGTH(stall_no)')
            ->orderBy('stall_no')
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.buildings.partials.stall-results', compact('building', 'stalls'));
        }

        return view('admin.buildings.show', compact('building', 'stalls'));
    }

    // Server-rendered hover panel for the dashboard map
    public function panel(Request $request, Building $building)
    {
        $stalls = $building->stalls()
            ->orderByRaw('LENGTH(stall_no)')
            ->orderBy('stall_no')
            ->get();
        $counts = $stalls->countBy(fn ($s) => $s->status);

        $summary = [
            'total' => $stalls->count(),
            'paid' => $counts->get('paid', 0),
            'partial' => $counts->get('partial', 0),
            'unpaid' => $counts->get('unpaid', 0),
            'overdue' => $counts->get('overdue', 0),
            'collected' => (float) $stalls->sum('amount_paid'),
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