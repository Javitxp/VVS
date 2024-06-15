<?php

namespace App\Services;

use Exception;

class StreamerDataManager
{
    private TokenProvider $tokenProvider;
    private StreamerDataProvider $streamerDataProvider;

    public function __construct(TokenProvider $tokenProvider, StreamerDataProvider $streamerDataProvider)
    {
        $this->tokenProvider = $tokenProvider;
        $this->streamerDataProvider = $streamerDataProvider;
    }

    /**
     * @throws Exception
     */
    public function getStreamerData($streamerId)
    {
        return $this->streamerDataProvider->getStreamerData($this->tokenProvider->getToken(), $streamerId);
    }
}
