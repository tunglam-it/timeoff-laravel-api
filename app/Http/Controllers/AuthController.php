<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Employees;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private $employees;

    public function __construct(Employees $employees)
    {
        $this->employees = $employees;
    }

    /***
     * register a new user
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $employees = $this->employees->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'Đăng ký thành công',
                'employees ' => $employees,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e, 'status' => $e->getCode()]);
        }
    }

    /***
     * Login into web
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Email hoặc Password không chính xác'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Could not create token', 'status' => $e->getCode()]);
        }
        return response()->json(['data' => auth()->user(), 'user_data' => $token]);
    }

    /***
     * get current user
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function profile()
    {
        return Auth::user();
    }

    /***
     * respond with token
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
        ]);
    }

    /***
     * refresh token
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json($this->respondWithToken(auth()->refresh()));
    }

    /***
     * logout
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User Logout']);
    }
}
