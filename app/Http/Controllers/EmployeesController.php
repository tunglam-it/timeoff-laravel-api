<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\Change;
use App\Models\Employees;
use App\Repositories\Employee\EmployeeRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployeesController extends Controller
{
    protected $employeesRepo;

    public function __construct(EmployeeRepository $employeesRepo)
    {
        $this->employeesRepo = $employeesRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $name = request()->input('name');
        $employees = $this->employeesRepo->filterAdmin($name);

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
        return $this->employeesRepo->find($id);
    }

    /***
     * Change password the specified resource from storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $this->employeesRepo->changePasswordUser();

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
        $employees = $this->employeesRepo->update($id,$request->all());
        return $employees;
    }

    /***
     * delete user
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {

        $this->employeesRepo->delete($id);
        return response()->json(['message' => 'Employee deleted']);
    }

}
