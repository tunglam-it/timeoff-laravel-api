<?php

namespace App\Repositories\Employee;

use App\Models\Employees;
use App\Models\Leaves;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{

    /**
     * get model
     * @return mixed
     */
    public function getModel()
    {
        return Employees::class;
    }

    /***
     * Display all employees but except admin
     * @param $name
     * @return mixed
     */
    public function filterAdmin($name)
    {
        if ($name) {
            $employees = Employees::whereIn('roles', [1, 2])->where('name', 'like', '%' . $name . '%')->get();
        } else {
            $employees = Employees::whereIn('roles', [1, 2])->get();
        }
        return $employees;
    }

    /***
     * update total_time field if leave has been approve
     * @param $id
     * @return mixed
     */
    public function updateTotalTime($id)
    {
        if (request()->status == Leaves::APPROVE_STATUS && request()->employee_id) {
            $employee = Employees::findOrFail(request()->employee_id);
            $estimate = Leaves::findOrFail($id)->estimate;
            $total_time = $employee->total_time;
            $employee->update(['total_time' => $total_time + $estimate]);
        }
    }

    /***
     * check and change password for user
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function changePasswordUser()
    {
        $user = Auth::user();

        if (!Hash::check(request()->oldPassword, $user->password)) {
            return response()->json([
                'message' => ' Password is incorrect'
            ], 401);
        }

        if (Hash::check(request()->password, $user->password)) {
            return response()->json([
                'message' => 'New password should not be the same as old password'
            ], 422);
        }

        $user->update([
            'password' => Hash::make(request()->password)
        ]);
    }
}
