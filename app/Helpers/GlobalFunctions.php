<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UserActivity;

if (!function_exists('arabicNumbers')) {
    /**
     * Convert English numbers to Arabic numerals.
     *
     * @param string|int $number
     * @return string
     */
    function arabicNumbers($number): string
    {
        $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishDigits = range(0, 9);

        return str_replace($englishDigits, $arabicDigits, (string) $number);
    }
}

if (!function_exists('formatLastUpdated')) {
    /**
     * Format the last updated timestamp into a human-readable string.
     *
     * @param string|null $lastUpdated
     * @return string
     */
    function formatLastUpdated($lastUpdated): string
    {
        if (!$lastUpdated) {
            return __('Never');
        }

        $lastUpdated = Carbon::parse($lastUpdated);
        $diffInMinutes = $lastUpdated->diffInMinutes(now());

        if ($diffInMinutes < 60) {
            return trans_choice(__('minute_ago'), $diffInMinutes, ['value' => $diffInMinutes]);
        }

        $diffInHours = (int) $lastUpdated->diffInHours(now());

        if ($diffInHours < 24) {
            $minutes = $diffInMinutes % 60;
            if ($minutes > 0) {
                return __('hour_minute_ago', ['hours' => $diffInHours, 'minutes' => $minutes]);
            }
            return trans_choice(__('hour_ago'), $diffInHours, ['value' => $diffInHours]);
        }

        $diffInDays = (int) $lastUpdated->diffInDays(now());
        return trans_choice(__('day_ago'), $diffInDays, ['value' => $diffInDays]);
    }
}

if (!function_exists('logError')) {
    /**
     * Log an error with consistent formatting.
     *
     * @param string $message
     * @param string $action
     * @param Throwable $e
     * @return void
     */
    function logError(string $message, string $action, Throwable $e): void
    {
        Log::error($message, [
            'error' => $e->getMessage(),
            'action' => $action,
            'user_id' => Auth::id(),
        ]);
    }
}

if (!function_exists('successResponse')) {
    /**
     * Return a standardized JSON success response, optionally with a redirect and extra data.
     *
     * @param string $message
     * @param string|null $redirect
     * @param array $data
     * @return JsonResponse
     */
    function successResponse(string $message, ?string $redirect = null, array $data = []): JsonResponse
    {
        $responseData = [
            'success' => true,
            'message' => $message,
        ];

        if ($redirect !== null) {
            $responseData['redirect'] = $redirect;
        }

        if (!empty($data)) {
            $responseData = array_merge($responseData, $data);
        }

        return response()->json($responseData, 200);
    }
}

if (!function_exists('errorResponse')) {
    /**
     * Return a standardized JSON error response.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    function errorResponse(string $error, int $statusCode): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $error,
            'message' => $error,
        ], $statusCode);
    }
}

if (!function_exists('userActivity')) {
    /**
     * Log a user activity in the UserActivity table.
     *
     * @param int $userId
     * @param string $activityType
     * @param string $description
     * @return void
     */
    function userActivity(int $userId, string $activityType, string $description): void
    {
        UserActivity::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
        ]);
    }
}

if (!function_exists('successResponse')) {
    /**
     * Return a standardized JSON success response, optionally with a redirect and extra data.
     *
     * @param string $message
     * @param string|null $redirect
     * @param array $data
     * @return JsonResponse
     */
    function successResponse(string $message, ?string $redirect = null, array $data = []): JsonResponse
    {
        $responseData = [
            'success' => true,
            'message' => $message,
        ];

        if ($redirect !== null) {
            $responseData['redirect'] = $redirect;
        }

        if (!empty($data)) {
            $responseData = array_merge($responseData, $data);
        }

        return response()->json($responseData, 200);
    }
}