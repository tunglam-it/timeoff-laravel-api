<?php

namespace App\Repositories\Leave;

use App\Http\Resources\LeaveResources;
use App\Models\Leaves;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class LeaveRepository extends BaseRepository implements LeaveRepositoryInterface
{

    /**
     * get model
     * @return mixed
     */
    public function getModel()
    {
        return Leaves::class;
    }

    /***
     * search and filter data by param, start_date, end_date, status of leaves
     * @param $param
     * @param $start_date
     * @param $end_date
     * @param $status
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchFilter($param, $start_date, $end_date, $status){
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

        $employees = $leaves->paginate(5000);
        if ($employees->isEmpty()) {
            return response()->json(['message' => ' Leaves not found'], 404);
        } else {
            return LeaveResources::collection($employees);
        }
    }

    /***
     * get all leaves by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function getLeavesById($id)
    {
        if (Auth::user()) {
            $user = Auth::user();
            $leaves = Leaves::where('employee_id', $id)->get();
            return response()->json(['leaves' => $leaves]);
        }
    }
}
