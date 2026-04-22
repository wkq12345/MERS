
<?php

namespace App\Http\Controllers;

use App\Models\UserDemographic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserDemographicController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'income' => ['required', 'numeric', 'min:0'],
        ]);

        $userId = Auth::id();

        if ($userId) {
            UserDemographic::updateOrCreate(
                ['user_id' => $userId],
                [
                    'age' => $validated['age'],
                    'gender' => $validated['gender'],
                    'income' => $validated['income'],
                ]
            );
        } else {
            $guestKey = (string) $request->session()->get('recommendation_guest_key', '');

            if ($guestKey === '') {
                $guestKey = (string) Str::uuid();
                $request->session()->put('recommendation_guest_key', $guestKey);
            }

            UserDemographic::updateOrCreate(
                ['guest_key' => $guestKey],
                [
                    'user_id' => null,
                    'guest_key' => $guestKey,
                    'age' => $validated['age'],
                    'gender' => $validated['gender'],
                    'income' => $validated['income'],
                ]
            );
        }

        return back()->with('success', 'Your demographic information has been saved.');
    }
}
