<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Criteria;
use App\Models\CriteriaType;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    public function index()
    {
        $criteria = Criteria::orderBy('name')->paginate(15);
        return view('SuperAdmin.criteria.index', compact('criteria'));
    }
    public function create()
    {
        $criteriaTypes = CriteriaType::orderBy('name')->get();
        return view('SuperAdmin.criteria.create', compact('criteriaTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:criteria,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'criteria_type_id' => ['required', 'exists:criteria_type,id'],
        ]);

        Criteria::create($validated);
        return redirect()->route('super-admin.criteria.index')->with('success', 'Criteria added successfully.');
    }

    public function edit(Criteria $criteria)
    {
        $criteriaTypes = CriteriaType::orderBy('name')->get();
        return view('SuperAdmin.criteria.edit', compact('criteria', 'criteriaTypes'));
    }

    public function update(Request $request, Criteria $criteria)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:criteria,name,' . $criteria->id],
            'description' => ['nullable', 'string', 'max:255'],
            'criteria_type_id' => ['required', 'exists:criteria_type,id'],

        ]);
        $criteria->update($validated);
        return redirect()->route('super-admin.criteria.index')->with('success', 'Criteria updated successfully.');
    }

    public function destroy(Criteria $criteria)
    {
        try {
            $criteria->delete();
            return redirect()->route('super-admin.criteria.index')->with('success', 'Criteria deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('super-admin.criteria.index')->with('error', 'Unable to delete criteria: ' . $e->getMessage());
        }
    }
}
