<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSettingsController extends Controller
{
    // Show the settings page
    public function index()
    {
        // Fetch the settings as key-value pairs
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.setting', compact('settings'));
    }

    // Update reservation settings
    public function updateReservationSettings(Request $request)
    {
        // Validate the request data
        $request->validate([
            'reservation_start_datetime' => 'required|date_format:Y-m-d\TH:i',
            'reservation_end_datetime' => 'required|date_format:Y-m-d\TH:i|after:reservation_start_datetime',
            'reservation_status' => 'required|in:open,closed',
            'eligible_students' => 'required|in:new,returning,all',
        ]);

        // Update or create the settings in the database
        Setting::updateOrCreate(
            ['key' => 'reservation_start_datetime'],
            ['value' => $request->reservation_start_datetime]
        );
        Setting::updateOrCreate(
            ['key' => 'reservation_end_datetime'],
            ['value' => $request->reservation_end_datetime]
        );
        Setting::updateOrCreate(
            ['key' => 'reservation_status'],
            ['value' => $request->reservation_status]
        );
        Setting::updateOrCreate(
            ['key' => 'eligible_students'],
            ['value' => $request->eligible_students]
        );

        // Redirect back with success message
        return redirect()->route('admin.setting')->with('success', 'Reservation settings updated successfully.');
    }
}
