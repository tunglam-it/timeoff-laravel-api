<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\Change;
use App\Models\Employees;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployeesController extends Controller
{
    private $employees;

    public function __construct(Employees $employees)
    {
        $this->employees = $employees;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $param = request()->input('param');

        if ($param) {
            $employees = $this->employees->whereIn('roles', [1, 2])->where('name', 'like', '%' . $param . '%')->get();
        } else {
            $employees = $this->employees->whereIn('roles', [1, 2])->get();
        }

        if ($employees->isEmpty()) {
            return response()->json(['message' => 'Employees not found'], 404);
        } else {
            return $employees;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->employees->find($id);
    }

    /***
     * unknow
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getUsers(Request $request)
    {
        $query = $this->employees->query();

        if ($request->has('roles')) {
            $query->where('roles', $request->input('roles'));
        }
        if ($request->has('day') && $request->has('month') && $request->has('year')) {
            $date = $request->input('year') . '-' . $request->input('month') . '-' . $request->input('day');
            $query->whereDate('created_at', $date);
        }
        $users = $query->get();
        return $users;
    }


    /***
     * Change password the specified resource from storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json([
                'message' => ' Password is incorrect'
            ], 401);
        }

        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'New password should not be the same as old password'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password updated successfully'
        ], 200);
    }

    /***
     * update user
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $employees = $this->employees->findOrFail($id);
        $employees->update($request->all());
        return $employees;
    }

    /***
     * delete user
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {

        $user = $this->employees->findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Employee deleted']);
    }

}
