<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = UserActivity::where('user_id', Auth::id())
            ->recent()
            ->get();

        return view('activities.index', compact('activities'));
    }

    // Example of logging activities in different scenarios
    public function logReservationConfirmation()
    {
        UserActivity::logActivity(
            Auth::id(),
            'Reservation Confirmed',
            'Your room reservation for the upcoming term has been confirmed'
        );
    }

    public function logDocumentUpload()
    {
        UserActivity::logActivity(
            Auth::id(),
            'Documents Uploaded',
            'Housing registration documents successfully submitted'
        );
    }
}
