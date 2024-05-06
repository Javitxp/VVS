<?php

namespace App\Services;

class UsersManager
{
    private CurlManager $curlManager;
    public function __construct(CurlManager $curlManager){
        $this->curlManager = $curlManager;
    }

    public function getUsers(String $id){
        $url = 'https://api.twitch.tv/helix/users?id='.$id;

        $users = $this->curlManager->getCurlResponse($url);

        return $users;
    }
}
