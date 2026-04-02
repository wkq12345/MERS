<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SusSubmission extends Controller
{
    public function submit(Request $request)
    {
        $user = auth()->user();
        $feedback = $request->input('feedback');
        $alreadySubmitted = $user->susFeedback()->where('created_at', '>=', now()->subDay())->exists();

        if ($alreadySubmitted) {
            return redirect()->back()->with('error', 'You have already submitted SUS feedback in the last 24 hours.');
        }

        $user->susFeedback()->create([
            'feedback' => $feedback,
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }
}
