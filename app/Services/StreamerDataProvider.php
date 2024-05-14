<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;
use App\Utilities\ErrorCodes;
use Exception;

class StreamerDataProvider
{
    private String $streamerId;
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getStreamerData($token)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $url = 'https://api.twitch.tv/helix/users?id='.$this->streamerId;
        try {
            $response = $this->apiClient->makeCurlCall($url, $headers);
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::STREAMERS_500);
        }
        $data = json_decode($response, true)['data'];
        return $data;
    }

    /**
     * @param String $streamerId
     */
    public function setStreamerId(string $streamerId): void
    {
        $this->streamerId = $streamerId;
    }
}
