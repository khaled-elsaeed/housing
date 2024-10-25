<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    public function showApplicantPage(){
        return view('admin.applicant.view');
    }
}
