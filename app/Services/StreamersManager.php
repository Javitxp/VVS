<?php

namespace App\Services;

class StreamersManager
{
    private CurlManager $curlManager;
    public function __construct(CurlManager $curlManager)
    {
        $this->curlManager = $curlManager;
    }

    public function getStreamers(String $id)
    {
        $url = 'https://api.twitch.tv/helix/users?id='.$id;

        $users = $this->curlManager->getCurlResponse($url);

        return $users;
    }
}
