<?php

namespace App\Services;

use Exception;

class UserDataManager
{
    private UserDataProvider $userDataProvider;

    public function __construct(UserDataProvider $userDataProvider)
    {
        $this->userDataProvider = $userDataProvider;
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
        return $this->userDataProvider->getUserFollowedStreamersTimeline($userId);
    }
}
