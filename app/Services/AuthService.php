<?php

namespace App\Services;

use App\Repositories\AuthRepository;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function registerUser(array $data) {
       return $this->authRepository->createUser($data);
    }
}