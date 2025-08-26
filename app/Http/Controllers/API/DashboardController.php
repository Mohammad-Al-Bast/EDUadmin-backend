<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard summary statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary(Request $request)
    {
        try {
            // Get basic counts
            $studentsCount = Student::count();
            $verifiedUsersCount = User::where('is_verified', true)->count();
            $totalUsersCount = User::count();
            $unverifiedUsersCount = $totalUsersCount - $verifiedUsersCount;

            // Get current date and time
            $currentDateTime = Carbon::now();

            // Chart data: Students by year
            $studentsByYear = Student::select('year', DB::raw('count(*) as count'))
                ->whereNotNull('year')
                ->groupBy('year')
                ->orderBy('year')
                ->get()
                ->map(function ($item) {
                    return [
                        'year' => (int)$item->year,
                        'count' => (int)$item->count
                    ];
                });

            // Chart data: Students by campus
            $studentsByCampus = Student::select('campus', DB::raw('count(*) as count'))
                ->whereNotNull('campus')
                ->groupBy('campus')
                ->orderBy('campus')
                ->get()
                ->map(function ($item) {
                    return [
                        'campus' => $item->campus,
                        'count' => (int)$item->count
                    ];
                });

            // Chart data: Students by school
            $studentsBySchool = Student::select('school', DB::raw('count(*) as count'))
                ->whereNotNull('school')
                ->groupBy('school')
                ->orderBy('school')
                ->get()
                ->map(function ($item) {
                    return [
                        'school' => $item->school,
                        'count' => (int)$item->count
                    ];
                });

            // Chart data: Users verification status
            $usersVerificationData = [
                [
                    'label' => 'Verified Users',
                    'value' => $verifiedUsersCount,
                    'percentage' => $totalUsersCount > 0 ? round(($verifiedUsersCount / $totalUsersCount) * 100, 2) : 0
                ],
                [
                    'label' => 'Unverified Users',
                    'value' => $unverifiedUsersCount,
                    'percentage' => $totalUsersCount > 0 ? round(($unverifiedUsersCount / $totalUsersCount) * 100, 2) : 0
                ]
            ];

            // Chart data: Admin vs Regular users
            $adminUsersCount = User::where('is_admin', true)->count();
            $regularUsersCount = $totalUsersCount - $adminUsersCount;

            $usersRoleData = [
                [
                    'label' => 'Admin Users',
                    'value' => $adminUsersCount,
                    'percentage' => $totalUsersCount > 0 ? round(($adminUsersCount / $totalUsersCount) * 100, 2) : 0
                ],
                [
                    'label' => 'Regular Users',
                    'value' => $regularUsersCount,
                    'percentage' => $totalUsersCount > 0 ? round(($regularUsersCount / $totalUsersCount) * 100, 2) : 0
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => [
                        'students_count' => $studentsCount,
                        'verified_users_count' => $verifiedUsersCount,
                        'total_users_count' => $totalUsersCount,
                        'unverified_users_count' => $unverifiedUsersCount,
                        'admin_users_count' => $adminUsersCount,
                        'regular_users_count' => $regularUsersCount,
                    ],
                    'datetime' => [
                        'current_date' => $currentDateTime->format('Y-m-d'),
                        'current_time' => $currentDateTime->format('H:i:s'),
                        'formatted_datetime' => $currentDateTime->format('Y-m-d H:i:s'),
                        'human_readable' => $currentDateTime->format('l, F j, Y \a\t g:i A'),
                        'timezone' => $currentDateTime->timezoneName,
                    ],
                    'charts' => [
                        'students_by_year' => $studentsByYear,
                        'students_by_campus' => $studentsByCampus,
                        'students_by_school' => $studentsBySchool,
                        'users_verification' => $usersVerificationData,
                        'users_role' => $usersRoleData,
                    ]
                ],
                'message' => 'Dashboard summary retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed statistics for specific periods
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailedStats(Request $request)
    {
        try {
            // Users registered in last 7 days
            $usersLastWeek = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();

            // Users registered in last 30 days
            $usersLastMonth = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();

            // Students by semester
            $studentsBySemester = Student::select('semester', DB::raw('count(*) as count'))
                ->whereNotNull('semester')
                ->groupBy('semester')
                ->orderBy('semester')
                ->get()
                ->map(function ($item) {
                    return [
                        'semester' => $item->semester,
                        'count' => (int)$item->count
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'recent_activity' => [
                        'users_last_week' => $usersLastWeek,
                        'users_last_month' => $usersLastMonth,
                    ],
                    'additional_charts' => [
                        'students_by_semester' => $studentsBySemester,
                    ]
                ],
                'message' => 'Detailed statistics retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve detailed statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
