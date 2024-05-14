<?php

namespace App\Services;

use Exception;

class StreamsDataManager
{
    private TokenProvider $tokenProvider;
    private StreamsDataProvider $streamsDataProvider;
    public function __construct(TokenProvider $tokenProvider, StreamsDataProvider $streamsDataProvider)
    {
        $this->tokenProvider = $tokenProvider;
        $this->streamsDataProvider = $streamsDataProvider;
    }

    /**
     * @throws Exception
     */
    public function getStreams()
    {
        return $this->streamsDataProvider->execute($this->tokenProvider->getToken());
    }

}
