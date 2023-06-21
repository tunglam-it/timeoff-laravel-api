<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Leaves;
use App\Repositories\Leave\LeaveRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\LeaveResources;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    protected $leaveRepo;

    public function __construct(LeaveRepository $leaveRepo)
    {
        $this->leaveRepo = $leaveRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $param = request()->input('param');
        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');
        $status = request()->input('status');

        $leaves = Leaves::query();
        if ($param) {
            $leaves->whereHas('employees', function ($query) use ($param) {
                $query->where('name', 'like', '%' . $param . '%');
            });
        }
        if ($start_date && $end_date) {
            $leaves->whereBetween('start_date', [$start_date, $end_date]);
        }
        if ($status) {
            $leaves->where('status', $status);
        }

        $employees = $leaves->get();
        if ($employees->isEmpty()) {
            return response()->json(['message' => ' Leaves not found'], 404);
        } else {
            return LeaveResources::collection($employees);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['estimate'] = $this->timeOff($request->start_date, $request->end_date);
        return $this->leaveRepo->create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->leaveRepo->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($request->status == Leaves::APPROVE_STATUS && $request->employee_id) {
            $employee = Employees::findOrFail($request->employee_id);
            $estimate = $this->leaveRepo->find($id)->estimate;
            $total_time = $employee->total_time;
            $employee->update(['total_time' => $total_time + $estimate]);
        }
        $leaves = $this->leaveRepo->update($id, $request->except('employee_id'));
        return $leaves;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->leaveRepo->delete($id);
        return response()->json(['message' => 'Leave deleted']);
    }

    /***
     * calculate time
     * @param $start
     * @param $end
     * @return float|int
     */
    function calculateTimeOff($start, $end)
    {
        $start_time = Carbon::parse($start);
        $end_time = Carbon::parse($end);

        if ($start_time->hour < 8) {
            $start_time->setTime(8, 0, 0);
        } elseif ($start_time->hour > 17 || ($start_time->hour == 17 && $start_time->minute > 30)) {
            $start_time->addDay()->setTime(8, 0, 0);
        }

        if ($end_time->hour > 17 || ($end_time->hour == 17 && $end_time->minute > 30)) {
            $end_time->setTime(17, 30, 0);
        } elseif ($end_time->hour < 8 || ($end_time->hour == 8 && $end_time->minute < 0)) {
            $end_time->subDay()->setTime(17, 30, 0);
        }

        $off_days = $start_time->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, $end_time) + 1;

        $off_hours = $off_days * 9.5;

        if (!$start_time->isSameDay($end_time)) {
            $start_off_hours = 0;
            if (!$start_time->isWeekend()) {
                $start_off_hours = $start_time->copy()->setTime(17, 30, 0)->diffInMinutes($start_time) / 60;
            }

            $end_off_hours = 0;
            if (!$end_time->isWeekend()) {
                $end_off_hours = $end_time->diffInMinutes($end_time->copy()->setTime(8, 0, 0)) / 60;
            }

            $off_hours -= ($start_off_hours + $end_off_hours);
        }

        return $off_hours;
    }


    /***
     * call calculate function
     * @param $start_date
     * @param $end_date
     * @return float|int
     */
    public function timeOff($start_date, $end_date)
    {
        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);
        return $this->calculateTimeOff($start, $end);
    }

    /***
     * get all leaves by userId
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function getLeavesByUserId()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $leaves = Leaves::where('employee_id', $user->id)->get();
            return response()->json(['leaves' => $leaves]);
        }
    }
}
