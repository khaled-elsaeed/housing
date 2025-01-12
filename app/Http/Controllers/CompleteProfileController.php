<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Program;

class CompleteProfileController extends Controller
{
    /**
     * Show the Complete Profile form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $faculties = Faculty::all();
        $governorates = Governorate::all();
        $cities = City::all();
        $programs = Program::all();

        return view('student.complete-profile', compact('user', 'faculties', 'governorates', 'cities','programs'));
    }
}
