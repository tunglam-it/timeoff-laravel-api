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

    public function register(RegisterRequest $request)
    {
        try {
            $employees = $this->employees->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'Employee Created Successfully',
                'employees ' => $employees,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e, 'status' => $e->getCode()]);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid Credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Could not create token', 'status' => $e->getCode()]);
        }
        return response()->json(['data' => auth()->user(), 'user_data' => $token]);
    }

    public function profile()
    {
        return Auth::user();
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
        ]);
    }

    public function refresh()
    {
        return response()->json($this->respondWithToken(auth()->refresh()));
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User Logout']);
    }

    public function delete($id)
    {

        $user = $this->employees->findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    /**
     * Change password the specified resource from storage.
     */
    public function change(Change $request)
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

    public function update(Request $request, $id)
    {
        $employees = $this->employees->findOrFail($id);
        $employees->update($request->all());
        return $employees;
    }
}
