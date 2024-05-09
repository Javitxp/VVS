<?php

namespace App\Services;

class StreamsDataManager
{
    private TokenProvider $tokenProvider;
    private StreamsDataProvider $streamsDataProvider;
    public function __construct(TokenProvider $tokenProvider, StreamsDataProvider $streamsDataProvider)
    {
        $this->tokenProvider = $tokenProvider;
        $this->streamsDataProvider = $streamsDataProvider;
    }

    public function getStreams()
    {
        $response = $this->streamsDataProvider->execute($this->tokenProvider->getToken());
        return $response;
    }

}
