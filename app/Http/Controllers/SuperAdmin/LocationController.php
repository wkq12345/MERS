<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'image_file' => ['nullable', 'image', 'max:2048'],
            'image_url' => ['nullable', 'string', 'max:2048', 'url'],
        ]);

        if (! $request->hasFile('image_file') && empty($validated['image_url'])) {
            return back()
                ->withErrors(['image' => 'Please upload an image or provide an image URL.'])
                ->withInput();
        }

        if ($request->hasFile('image_file')) {
            $validated['image'] = Storage::disk('public')->putFile('locations', $request->file('image_file'));
        } else {
            $validated['image'] = $validated['image_url'];
        }

        unset($validated['image_file'], $validated['image_url']);

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
            'image_file' => ['nullable', 'image', 'max:2048'],
            'image_url' => ['nullable', 'string', 'max:2048', 'url'],
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = Storage::disk('public')->putFile('locations', $request->file('image_file'));
        } elseif (! empty($validated['image_url'])) {
            $validated['image'] = $validated['image_url'];
        }

        unset($validated['image_file'], $validated['image_url']);

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
