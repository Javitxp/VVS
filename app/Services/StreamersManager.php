<?php

namespace App\Services;

class StreamersManager
{
    private CurlManager $curlManager;
    public function __construct(CurlManager $curlManager)
    {
        $this->curlManager = $curlManager;
    }

    public function getStreamers(String $streamerId)
    {
        $url = 'https://api.twitch.tv/helix/users?id='.$streamerId;

        $users = $this->curlManager->getCurlResponse($url);

        return $users;
    }
}
