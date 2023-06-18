<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid Token !!!'], 401);
        });
        $this->renderable(function (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has Expired !!!'], 401);
        });
        $this->renderable(function (JWTException $e) {
            return response()->json(['error' => 'Token not parsed']);
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ], 404);
            }
        });

    }
}
