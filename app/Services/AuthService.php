<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected $authRepository;
   
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function registerUser(array $data)
    {
        return $this->authRepository->createUser($data);
    }

    public function loginUser(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }
       
        $user = Auth::user();
        $token = $user->createToken('SocialMediaApp')->accessToken;


        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function logoutUser()
    {
        return Auth::user()->token()->revoke();
    }
}
