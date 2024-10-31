<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Criteria;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    public function index()
    {
        // Fetch all fields with their criteria
        $fields = Field::with('criteria')->get();

        // Return the view with the fields data
        return view('admin.reservation.criteria', compact('fields'));
    }
}

