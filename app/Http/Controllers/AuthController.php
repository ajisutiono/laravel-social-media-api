<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

// use Illuminate\Http\Request;


class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->registerUser($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $result = $this->authService->loginUser($credentials);

        if (!$result) {
            return response()->json([
                'error' => 'Invalid email or password',
            ], 401);
        }

        // return response()->json([
        //     'message' => 'Login success',
        //     'user'    => $result['user'],
        //     'token'   => $result['token'],
        // ], 200);

        return response()->json([
            'message' => 'Login success',
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type'   => 'Bearer',
            'expires_in'   => 3600,
        ], 200);
    }
}
