<?php

namespace App\Services;

class StreamerDataManager
{
    private $tokenProvider;
    private $streamerDataProvider;

    public function __construct(TokenProvider $tokenProvider, StreamerDataProvider $streamerDataProvider)
    {
        $this->tokenProvider = $tokenProvider;
        $this->streamerDataProvider = $streamerDataProvider;
    }

    public function getStreamerData()
    {
        $response = $this->streamerDataProvider->getStreamerData($this->tokenProvider->getToken());
        return $response;
    }

    public function setStreamerId($streamerId): void
    {
        $this->streamerDataProvider->setStreamerId($streamerId);
    }

}
