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
        $streams = $this->streamsDataProvider->execute($this->tokenProvider->getToken());
        $filteredStreams = [];
        foreach ($streams as $stream) {
            $filteredStream = [
                'title' => $stream['title'],
                'user_name' => $stream['user_name']
            ];
            $filteredStreams[] = $filteredStream;
        }

        return $filteredStreams;
    }

}
