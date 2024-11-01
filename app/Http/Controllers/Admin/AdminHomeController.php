<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminHomeService;

class AdminHomeController extends Controller
{
    protected $homeService;

    public function __construct(AdminHomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function index()
    {
        $data = $this->homeService->getHomeData();

        return view('admin.home', [
            'total_students' => $data['total_students'],
            'male_students' =>$data['male_students'],
            'female_students' =>$data['female_students'],
            'last_updated_room' => $data['last_updated_room'],
            'occupancy_rate' => $data['occupancy_rate'],
            'total_students' => $data['total_students'],
            'last_create_student' => $data['last_create_student'],
            'last_created_male_student' => $data['last_created_male_student'],
            'last_created_female_student' => $data['last_created_female_student'],
            'buildings' => $data['buildings'],
        ]);
       
    }
}
