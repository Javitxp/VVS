<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;
use App\Utilities\ErrorCodes;
use Exception;

class UserDataProvider
{
    private ApiClient $apiClient;
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws Exception
     */
    public function getUserData($token, $userId)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $url = 'https://api.twitch.tv/helix/users?id='.$userId;
        try {
            $response = $this->apiClient->makeCurlCall($url, $headers);
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::USERS_500);
        }
        return json_decode($response, true)['data'];
    }
}
