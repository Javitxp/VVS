<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;

class StreamsDataProvider
{
    private ApiClient $apiClient;
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }
    public function execute()
    {
        $url = 'https://api.twitch.tv/helix/streams';
        $headers = array('Authorization: Bearer '.$this->apiClient->getToken());
        $response = $this->apiClient->makeCurlCall($url, $headers);
        $data = json_decode($response, true)['data'];
        return $data;
    }
}
