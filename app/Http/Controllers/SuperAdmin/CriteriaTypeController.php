<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CriteriaType;
use Illuminate\Http\Request;

class CriteriaTypeController extends Controller
{
    public function index()
    {
        $types = CriteriaType::withCount('criteria')->orderBy('name')->paginate(15);
        return view('SuperAdmin.criteria_types.index', compact('types'));
    }

    public function create()
    {
        return view('SuperAdmin.criteria_types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:criteria_type,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'ideal_preference' => ['nullable', 'string', 'max:255'],
        ]);

        CriteriaType::create($validated);

        return redirect()->route('super-admin.criteria_types.index')->with('success', 'Criteria type added successfully.');
    }

    public function edit(CriteriaType $criteria_type)
    {
        return view('SuperAdmin.criteria_types.edit', ['type' => $criteria_type]);
    }

    public function update(Request $request, CriteriaType $criteria_type)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:criteria_type,name,' . $criteria_type->id],
            'description' => ['nullable', 'string', 'max:255'],
            'ideal_preference' => ['nullable', 'string', 'max:255'],
        ]);

        $criteria_type->update($validated);

        return redirect()->route('super-admin.criteria_types.index')->with('success', 'Criteria type updated successfully.');
    }

    public function destroy(CriteriaType $criteria_type)
    {
        try {
            $criteria_type->delete();
            return redirect()->route('super-admin.criteria_types.index')->with('success', 'Criteria type deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('super-admin.criteria_types.index')->with('error', 'Unable to delete: ' . $e->getMessage());
        }
    }
}
