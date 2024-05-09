<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;
use App\Utilities\ErrorCodes;
use Exception;

class UserDataProvider
{
    private String $userId;
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getUserData($token)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $url = 'https://api.twitch.tv/helix/users?id='.$this->userId;
        try {
            $response = $this->apiClient->makeCurlCall($url, $headers);
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::USERS_500);
        }
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
