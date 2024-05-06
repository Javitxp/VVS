<?php

namespace App\Services;

class StreamsManager
{
    private CurlManager $curlManager;
    public function __construct(CurlManager $curlManager){
        $this->curlManager = $curlManager;
    }

    public function getStreams(){
        $url = 'https://api.twitch.tv/helix/streams';

        $streams = $this->curlManager->getCurlResponse($url);

        return $streams;
    }
}
