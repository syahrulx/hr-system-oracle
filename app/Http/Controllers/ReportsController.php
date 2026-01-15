<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest as EmployeeRequest;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->subMonth()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        $staffId = $request->input('staff_id');

        // Get all employees (excluding owner) - include leave balances
        $allEmployees = User::whereRaw("LOWER(user_role) != 'owner'")
            ->select('user_id as id', 'name', 'annual_leave_balance', 'sick_leave_balance', 'emergency_leave_balance')
            ->get();

        $employeeIds = $staffId ? [$staffId] : $allEmployees->pluck('id')->toArray();

        // Debug: Log database counts
        $sampleShifts = DB::table('shift_schedules')->select('shift_date')->limit(5)->get();
        $shiftsInMonth = DB::table('shift_schedules')
            ->whereRaw("TO_CHAR(shift_date, 'YYYY-MM') = ?", [$month])
            ->count();
        \Log::info('Reports Debug', [
            'month' => $month,
            'year' => $year,
            'monthNum' => $monthNum,
            'total_employees' => count($employeeIds),
            'total_attendances' => DB::table('attendances')->count(),
            'total_shifts' => DB::table('shift_schedules')->count(),
            'shifts_in_requested_month' => $shiftsInMonth,
            'sample_shift_dates' => $sampleShifts->pluck('shift_date')->toArray(),
        ]);

        // BULK QUERY: Get all attendance stats in one query - JOIN with shift_schedules to filter by month
        $attendanceStats = DB::table('attendances')
            ->join('shift_schedules', 'attendances.shift_id', '=', 'shift_schedules.shift_id')
            ->select(
                'attendances.user_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(CASE WHEN attendances.status != \'missed\' THEN 1 END) as present'),
                DB::raw('COUNT(CASE WHEN attendances.status = \'missed\' THEN 1 END) as absent'),
                DB::raw('COUNT(CASE WHEN attendances.status = \'on_time\' THEN 1 END) as on_time'),
                DB::raw('COUNT(CASE WHEN attendances.status = \'late\' THEN 1 END) as late')
            )
            ->whereIn('attendances.user_id', $employeeIds)
            ->whereRaw("TO_CHAR(shift_schedules.shift_date, 'YYYY') = ?", [$year])
            ->whereRaw("TO_CHAR(shift_schedules.shift_date, 'MM') = ?", [$monthNum])
            ->groupBy('attendances.user_id')
            ->get()
            ->keyBy('user_id');

        // Debug: Check the joined attendance count
        $joinedCount = DB::table('attendances')
            ->join('shift_schedules', 'attendances.shift_id', '=', 'shift_schedules.shift_id')
            ->count();
        $joinedMonthCount = DB::table('attendances')
            ->join('shift_schedules', 'attendances.shift_id', '=', 'shift_schedules.shift_id')
            ->whereRaw("TO_CHAR(shift_schedules.shift_date, 'YYYY-MM') = ?", [$month])
            ->count();
        \Log::info('Attendance Debug', [
            'joined_total' => $joinedCount,
            'joined_in_month' => $joinedMonthCount,
            'stats_count' => $attendanceStats->count(),
            'stats' => $attendanceStats->toArray(),
        ]);

        // Compute useful metrics for owners
        $monthStart = Carbon::createFromDate((int) $year, (int) $monthNum, 1)->startOfMonth();
        $monthEnd = Carbon::createFromDate((int) $year, (int) $monthNum, 1)->endOfMonth();

        // 1) Working hours per staff (sum of daily hours for present/late records with both times)
        $attendanceWithTimes = Attendance::whereIn('attendances.user_id', $employeeIds)
            ->join('shift_schedules', 'attendances.shift_id', '=', 'shift_schedules.shift_id')
            ->whereNotNull('clock_in_time')
            ->whereNotNull('clock_out_time')
            ->whereRaw("TO_CHAR(shift_schedules.shift_date, 'YYYY') = ?", [$year])
            ->whereRaw("TO_CHAR(shift_schedules.shift_date, 'MM') = ?", [$monthNum])
            ->select('attendances.*')
            ->get();
        $secondsByEmp = [];
        $daysWithTimesByEmp = [];
        foreach ($attendanceWithTimes as $att) {
            $in = Carbon::parse($att->clock_in_time);
            $out = Carbon::parse($att->clock_out_time);
            $diff = max(0, $out->diffInSeconds($in, false));
            $secondsByEmp[$att->user_id] = ($secondsByEmp[$att->user_id] ?? 0) + $diff;
            $daysWithTimesByEmp[$att->user_id] = ($daysWithTimesByEmp[$att->user_id] ?? 0) + 1;
        }

        // 2) Approved leaves per staff (status = 1 assumed approved)
        $approvedLeaves = EmployeeRequest::whereIn('user_id', $employeeIds)
            ->whereRaw("TO_CHAR(start_date, 'YYYY') = ?", [$year])
            ->whereRaw("TO_CHAR(start_date, 'MM') = ?", [$monthNum])
            ->where('status', 1)
            ->select('user_id', DB::raw('COUNT(*) as approved'))
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // Debug: Log approved leaves
        \Log::info('Approved Leaves Debug', [
            'total_leave_requests' => EmployeeRequest::count(),
            'approved_in_period' => $approvedLeaves->count(),
            'data' => $approvedLeaves->toArray(),
        ]);

        // Calculate summary stats
        $totalEmployees = $allEmployees->count();
        $totalPresent = $attendanceStats->sum('present');
        $totalAbsent = $attendanceStats->sum('absent');
        $totalRecords = $totalPresent + $totalAbsent;
        $attendanceRate = $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100, 1) : 0;

        // Calculate average daily hours across all staff with recorded times
        $totalSecondsAll = array_sum($secondsByEmp);
        $totalDaysAll = array_sum($daysWithTimesByEmp);
        $avgDailyHours = $totalDaysAll > 0 ? round(($totalSecondsAll / 3600) / $totalDaysAll, 1) : 0;

        // Find top performer
        $topPerformer = ['name' => 'No Data', 'attendance_rate' => 0];
        $highestRate = 0;
        foreach ($attendanceStats as $empId => $stats) {
            $rate = $stats->total > 0 ? ($stats->present / $stats->total) * 100 : 0;
            if ($rate > $highestRate) {
                $highestRate = $rate;
                $employee = $allEmployees->firstWhere('id', $empId);
                $topPerformer = [
                    'name' => $employee->name ?? 'Unknown',
                    'attendance_rate' => round($rate, 1)
                ];
            }
        }

        $summary = [
            ['label' => 'Employees', 'value' => $totalEmployees],
            ['label' => 'Attendance Rate', 'value' => $attendanceRate . '%'],
            ['label' => 'Top Performer', 'value' => $topPerformer['name'] . ' (' . $topPerformer['attendance_rate'] . '%)'],
        ];

        // Build staff arrays
        $employeesToDisplay = $staffId ? $allEmployees->where('id', $staffId) : $allEmployees;

        $staffAttendance = [];
        $staffTasks = [];
        $staffRanking = [];
        $staffHours = [];
        $staffLeaves = [];

        foreach ($employeesToDisplay as $employee) {
            $empId = $employee->id;
            $attendStats = $attendanceStats->get((string) $empId);

            // Debug: Log what we're looking up
            \Log::info('Staff lookup', [
                'empId' => $empId,
                'empId_type' => gettype($empId),
                'attendStats_found' => $attendStats !== null,
                'present' => $attendStats->present ?? 'null',
            ]);

            // Attendance breakdown
            $staffAttendance[] = [
                'name' => $employee->name,
                'present' => (int) ($attendStats->present ?? 0),
                'absent' => (int) ($attendStats->absent ?? 0),
                'late' => (int) ($attendStats->late ?? 0),
                'on_time' => (int) ($attendStats->on_time ?? 0),
            ];

            // Working hours and approved leaves
            $totalSeconds = $secondsByEmp[$empId] ?? 0;
            $daysCount = $daysWithTimesByEmp[$empId] ?? 0;
            $totalHours = round($totalSeconds / 3600, 1);
            $avgDailyHours = $daysCount > 0 ? round(($totalSeconds / 3600) / $daysCount, 1) : 0.0;
            $staffHours[] = [
                'name' => $employee->name,
                'totalHours' => $totalHours,
                'avgDailyHours' => $avgDailyHours,
            ];

            $staffLeaves[] = [
                'name' => $employee->name,
                'approved' => (int) ($approvedLeaves->get((string) $empId)->approved ?? 0),
                'annual_balance' => (int) ($employee->annual_leave_balance ?? 0),
                'sick_balance' => (int) ($employee->sick_leave_balance ?? 0),
                'emergency_balance' => (int) ($employee->emergency_leave_balance ?? 0),
            ];

            // Ranking
            $attendanceRate = ($attendStats->total ?? 0) > 0 ? round(($attendStats->present / $attendStats->total) * 100, 1) : 0;
            $overallScore = ($attendanceRate * 0.7);

            $staffRanking[] = [
                'name' => $employee->name,
                'attendance' => $attendanceRate,
                'score' => round($overallScore, 1),
            ];
        }

        // Sort ranking by score
        usort($staffRanking, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Debug: Final data check
        \Log::info('Final staffAttendance', $staffAttendance);

        return Inertia::render('Reports/Reports', [
            'summary' => $summary,
            'staffAttendance' => $staffAttendance,
            'staffTasks' => [],
            'staffHours' => $staffHours,
            'staffLeaves' => $staffLeaves,
            'staffRanking' => $staffRanking,
            'month' => $month,
            'allStaff' => $allEmployees,
            'selectedStaffId' => $staffId,
        ]);
    }

}