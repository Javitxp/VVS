<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;

class UserDataProvider
{
    private ApiClient $apiClient;
    private String $userId;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getUserData() {
        if(!isset($this->userId)){
            http_response_code(500);
            return json_encode(['message' => 'Parameter id required']);
        }
        $headers = array('Authorization: Bearer '.$this->apiClient->getToken());
        $url = 'https://api.twitch.tv/helix/users?id='.$this->userId;
        $response = $this->apiClient->makeCurlCall($url, $headers);
        $data = json_decode($response, true)['data'];
        return $data;
    }

    /**
     * @param String $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }
}
