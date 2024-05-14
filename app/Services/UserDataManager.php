<?php

namespace App\Services;

use Exception;

class UserDataManager
{
    private TokenProvider $tokenProvider;
    private UserDataProvider $userDataProvider;

    public function __construct(TokenProvider $tokenProvider, UserDataProvider $userDataProvider)
    {
        $this->tokenProvider = $tokenProvider;
        $this->userDataProvider = $userDataProvider;
    }

    /**
     * @throws Exception
     */
    public function getUserData($userId)
    {
        return $this->userDataProvider->getUserData($this->tokenProvider->getToken(), $userId);
    }
}
