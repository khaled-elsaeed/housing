<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminHomeService;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AdminHomeController extends Controller
{
    protected $homeService;

    /**
     * Constructor to inject the AdminHomeService.
     *
     * @param AdminHomeService $homeService
     */
    public function __construct(AdminHomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function showDashboard()
    {
        try {
            if (Gate::denies('is-admin')) {
                return response()->view('errors.403', [], 403);
            }

            return view('admin.home');
        } catch (Exception $e) {
            Log::error('Error rendering admin home: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->view('errors.505');
        }
    }

    /**
     * Fetch dashboard statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchStats()
    {
        try {
            if (Gate::denies('is-admin')) {
                return response()->json([
                    'error' => 'Unauthorized access to statistics data.'
                ], 403);
            }

            $stats = $this->homeService->getHomeData();

            $data = [
                'total_students' => $stats['total_students'],
                'male_students' => $stats['male_students'],
                'female_students' => $stats['female_students'],
                'last_updated_room' => $stats['last_updated_room'],
                'occupancy_rate' => $stats['occupancy_rate'],
                'last_create_student' => $stats['last_create_student'],
                'last_created_male_student' => $stats['last_created_male_student'],
                'last_created_female_student' => $stats['last_created_female_student'],
                'buildings' => $stats['buildings'],
            ];

            return response()->json($data);
        } catch (Exception $e) {
            Log::error('Error retrieving dashboard statistics: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Unable to retrieve dashboard data at this time.'
            ], 500);
        }
    }
}
