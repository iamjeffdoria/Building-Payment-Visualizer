<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Stall;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StallController extends Controller
{
    public function store(Request $request, Building $building)
    {
        $validated = $request->validate([
            'stall_no' => [
                'nullable', 'string', 'max:50',
                Rule::unique('stalls', 'stall_no')->where('building_id', $building->id),
            ],
            'tenant_name' => ['required', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:paid,unpaid,overdue'],
        ]);

        $stallNo = $validated['stall_no'] ?? null;

        if (!$stallNo) {
            $n = $building->stalls()->count() + 1;
            do {
                $stallNo = $building->short . '-' . str_pad($n++, 2, '0', STR_PAD_LEFT);
            } while ($building->stalls()->where('stall_no', $stallNo)->exists());
        }

        $building->stalls()->create([
            'stall_no' => $stallNo,
            'tenant_name' => $validated['tenant_name'],
            'amount' => $validated['amount'] ?? $building->rent,
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Stall ' . $stallNo . ' added.');
    }

    public function update(Request $request, Stall $stall)
    {
        $validated = $request->validate([
            'stall_no' => [
                'required', 'string', 'max:50',
                Rule::unique('stalls', 'stall_no')
                    ->where('building_id', $stall->building_id)
                    ->ignore($stall->id),
            ],
            'tenant_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:paid,unpaid,overdue'],
        ]);

        $stall->update($validated);

        return back()->with('success', 'Stall updated.');
    }

    public function destroy(Stall $stall)
    {
        $stall->delete();

        return back()->with('success', 'Stall removed.');
    }
}