<?php

namespace App\Services;

class UserDataManager
{
    
    private $tokenProvider;
    private $userDataProvider;

    public function __construct(TokenProvider $tokenProvider, UserDataProvider $userDataProvider)
    {
        $this->tokenProvider = $tokenProvider;
        $this->userDataProvider = $userDataProvider;
    }

    public function getUserData()
    {
        $response = $this->userDataProvider.getUserData();
        return $response;
    }

}
