<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('name')->paginate(15);
        return view('SuperAdmin.locations.index', compact('locations'));
    }
    public function create()
    {
        return view('SuperAdmin.locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:location,name'],
        ]);

        Location::create($validated);
        return redirect()->route('super-admin.locations.index')->with('success', 'Location added successfully.');
    }

    public function edit(Location $location)
    {
        return view('SuperAdmin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:location,name,' . $location->id],
        ]);
        $location->update($validated);
        return redirect()->route('super-admin.locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        try {
            $location->delete();
            return redirect()->route('super-admin.locations.index')->with('success', 'Location deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('super-admin.locations.index')->with('error', 'Unable to delete location: ' . $e->getMessage());
        }
    }
}
