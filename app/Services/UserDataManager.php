<?php

namespace App\Services;

use Exception;

class UserDataManager
{
    private UserDataProvider $userDataProvider;
    private TokenProvider $tokenProvider;

    public function __construct(UserDataProvider $userDataProvider, TokenProvider $tokenProvider)
    {
        $this->userDataProvider = $userDataProvider;
        $this->tokenProvider = $tokenProvider;
    }

    /**
     * @throws Exception
     */
    public function createUser($username, $password)
    {
        return $this->userDataProvider->createUser($username, $password);
    }
    /**
     * @throws Exception
     */
    public function getAllUsers()
    {
        return $this->userDataProvider->getAllUsers();
    }
    /**
     * @throws Exception
     */
    public function getUserFollowedStreamersTimeline($userId)
    {
        return $this->userDataProvider->getUserFollowedStreamersTimeline($this->tokenProvider->getToken(), $userId);
    }
    /**
     * @throws Exception
     */
    public function followStreamer($userId, $streamerId)
    {
        return $this->userDataProvider->followStreamer($userId, $streamerId);
    }
}
