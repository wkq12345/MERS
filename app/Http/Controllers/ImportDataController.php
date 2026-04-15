<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CombinedImport;
use Illuminate\Support\Facades\Log;

class ImportDataController extends Controller
{
    /**
     * Handle the data import request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        // Support legacy/new form field names by normalizing to 'type'.
        $request->merge([
            'type' => $request->input('type', $request->input('sheet')),
        ]);

        // 1. Validate the incoming file and the required import "type" (sheet name)
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
            'type' => 'required|string|in:import location,import criteria type,import criteria,import tourist spot,import rating',
        ]);

        try {
            $selectedSheet = $request->input('type');
            $file = $request->file('file');

            // 2. Process the import using the CombinedImport class
            Excel::import(new CombinedImport($selectedSheet), $file);

            // 3. Return a success response
            return redirect()->back()->with('success', 'Data imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Handle row-level validation errors from Excel
            $failures = $e->failures();
            Log::error('Excel Import Validation Error', ['failures' => $failures]);

            return redirect()->back()->with('error', 'There was a validation error in the imported data. Please check the format.');
        } catch (\Exception $e) {
            // Log general exceptions
            Log::error('Import process failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }
}
