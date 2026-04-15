<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Criteria;
use App\Models\CriteriaType;
use App\Models\Location;
use App\Models\TouristSpot;
use App\Models\TouristSpotCriteriaRating;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TouristSpotController extends Controller
{
    public function index()
    {
        $spots = TouristSpot::with('location')->orderBy('name')->paginate(15);
        return view('SuperAdmin.tourist_spots.index', compact('spots'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get();
        $criteriaTypes = CriteriaType::with(['criteria' => fn($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('SuperAdmin.tourist_spots.create', compact('locations', 'criteriaTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tourist_spots,name'],
            'description' => ['nullable', 'string'],
            'location_id' => ['nullable', 'exists:location,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_url' => ['nullable', 'url'],
            'status' => ['nullable', 'boolean'],
            'ratings.*' => ['nullable', 'integer', 'min:1', 'max:9'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $dir = public_path('images/uploads');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = Str::slug(pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME))
                . '-' . time() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move($dir, $filename);
            $imagePath = 'images/uploads/' . $filename;
        } elseif (!empty($validated['image_url'] ?? null)) {
            $imagePath = $validated['image_url'];
        }

        $spot = TouristSpot::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'location_id' => $validated['location_id'] ?? null,
            'image' => $imagePath,
            'status' => $validated['status'] ?? true,
        ]);

        $this->persistRatings($spot->id, $request->input('ratings', []));

        return redirect()->route('super-admin.tourist_spots.index')->with('success', 'Tourist spot added successfully.');
    }

    public function edit(TouristSpot $tourist_spot)
    {
        $locations = Location::orderBy('name')->get();
        $criteriaTypes = CriteriaType::with(['criteria' => fn($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        $ratings = TouristSpotCriteriaRating::where('tourist_spot_id', $tourist_spot->id)
            ->pluck('rating', 'criteria_id')
            ->toArray();

        return view('SuperAdmin.tourist_spots.edit', [
            'spot' => $tourist_spot,
            'locations' => $locations,
            'criteriaTypes' => $criteriaTypes,
            'ratings' => $ratings,
        ]);
    }

    public function update(Request $request, TouristSpot $tourist_spot)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tourist_spots,name,' . $tourist_spot->id],
            'description' => ['nullable', 'string'],
            'location_id' => ['nullable', 'exists:location,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_url' => ['nullable', 'url'],
            'status' => ['nullable', 'boolean'],
            'ratings.*' => ['nullable', 'integer', 'min:1', 'max:9'],
        ]);

        $imagePath = $tourist_spot->image;
        if ($request->hasFile('image')) {
            $dir = public_path('images/uploads');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = Str::slug(pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME))
                . '-' . time() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move($dir, $filename);
            $imagePath = 'images/uploads/' . $filename;
        } elseif (!empty($validated['image_url'] ?? null)) {
            $imagePath = $validated['image_url'];
        }

        $tourist_spot->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'location_id' => $validated['location_id'] ?? null,
            'image' => $imagePath,
            'status' => $validated['status'] ?? $tourist_spot->status,
        ]);

        TouristSpotCriteriaRating::where('tourist_spot_id', $tourist_spot->id)->delete();
        $this->persistRatings($tourist_spot->id, $request->input('ratings', []));

        return redirect()->route('super-admin.tourist_spots.index')->with('success', 'Tourist spot updated successfully.');
    }

    public function destroy(TouristSpot $tourist_spot)
    {
        try {
            $tourist_spot->delete();
            return redirect()->route('super-admin.tourist_spots.index')->with('success', 'Tourist spot deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('super-admin.tourist_spots.index')->with('error', 'Unable to delete: ' . $e->getMessage());
        }
    }

    private function persistRatings(int $spotId, array $ratings): void
    {
        if (empty($ratings)) {
            return;
        }

        $criteria = Criteria::whereIn('id', array_keys($ratings))->pluck('id')->all();

        foreach ($ratings as $criteriaId => $rating) {
            if ($rating === null || $rating === '' || !in_array($criteriaId, $criteria, true)) {
                continue;
            }

            TouristSpotCriteriaRating::create([
                'tourist_spot_id' => $spotId,
                'criteria_id' => $criteriaId,
                'rating' => (int) $rating,
            ]);
        }
    }
}
