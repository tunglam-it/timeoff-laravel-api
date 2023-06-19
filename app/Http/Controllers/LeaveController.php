<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Leaves;
use App\Repositories\Leave\LeaveRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\LeaveResources;

class LeaveController extends Controller
{
    const WORK_START_HOUR = 8;
    const WORK_END_HOUR = 17;
    const LUNCH_START_HOUR = 12;
    const LUNCH_END_HOUR = 13.5;
    const WEEKENDS = [Carbon::SATURDAY, Carbon::SUNDAY];

    protected $leaves;
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
            $leaves->whereHas('employees',function ($query) use ($param){
                $query->where('name','like','%'.$param.'%');
            });
        }
        if($start_date && $end_date){
            $leaves->whereBetween('start_date',[$start_date,$end_date]);
        }
        if($status){
            $leaves->where('status',$status);
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
//        dd($data['estimate']);
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
        if ($request->status == 1 && $request->employee_id) {
            $employee = Employees::findOrFail($request->employee_id);
            $estimate = $this->leaveRepo->find($id)->estimate;
            $total_time = $employee->total_time;
            $employee->update(['total_time' => $total_time+$estimate]);
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

    /****
     * Calculate timeoff
     * @param Carbon $start_time
     * @param Carbon $end_time
     * @return float|int
     */
    private function calculateTimeOff(Carbon $start_time, Carbon $end_time)
    {
        $total_work_hours = 0;

        // Duyệt từng ngày trong khoảng thời gian
        while ($start_time->lt($end_time)) {
            $curr_day = $start_time->copy()->startOfDay();

            // Nếu là ngày cuối tuần thì không tính
            if ($curr_day->isWeekend()) {
                $start_time->addDay();
                continue;
            }
            $start_hour = max($curr_day->copy()->hour(self::WORK_START_HOUR), $start_time);
            $end_hour = min($curr_day->copy()->hour(self::WORK_END_HOUR)->minute(30), $end_time);
            $lunch_start = $curr_day->copy()->hour(self::LUNCH_START_HOUR);
            $lunch_end = $curr_day->copy()->hour(self::LUNCH_END_HOUR);

            // Nếu có thời gian làm việc trong buổi sáng
            if ($start_hour->lt($lunch_start)) {
                $morning_end = min($end_hour, $lunch_start);
                $morning_hours = $morning_end->diffInMinutes($start_hour) / 60;
                $total_work_hours += $morning_hours;
            }

            // Nếu có thời gian làm việc trong buổi chiều
            if ($end_hour->gt($lunch_end)) {
                $afternoon_start = max($start_hour, $lunch_end);
                $afternoon_hours = $end_hour->diffInMinutes($afternoon_start) / 60;
                $total_work_hours += $afternoon_hours;
            }

            $start_time->addDay();
        }
        return $total_work_hours;
    }

    /***
     * call calculate function
     * @param Request $request
     * @return float|int
     */
    public function timeOff($start_date, $end_date)
    {
        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);
        return $this->calculateTimeOff($start, $end);
    }
}
