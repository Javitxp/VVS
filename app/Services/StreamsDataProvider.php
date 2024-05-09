<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;
use App\Utilities\ErrorCodes;
use Exception;

class StreamsDataProvider
{
    private ApiClient $apiClient;
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }
    public function execute($token)
    {
        $headers = array('Authorization: Bearer '. $token);
        $url = 'https://api.twitch.tv/helix/streams';
        try {
            $response = $this->apiClient->makeCurlCall($url, $headers);
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::STREAMS_500);
        }
        $data = json_decode($response, true)['data'];
        return $data;
    }
}
