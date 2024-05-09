<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;

class UserDataProvider
{
    private String $userId;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getUserData($token)
    {
        $headers = array('Authorization: Bearer ' . $token);
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
