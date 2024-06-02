<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;
use App\Utilities\ErrorCodes;
use Exception;

class StreamerDataProvider
{
    private ApiClient $apiClient;
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws Exception
     */
    public function getStreamerData($token, $streamerId)
    {
        $headers = array('Authorization: Bearer ' . $token);
        $url = 'https://api.twitch.tv/helix/users?id='.$streamerId;
        try {
            $response = $this->apiClient->makeCurlCall($url, $headers);

            $decResponse = json_decode($response, true);
            if (isset($decResponse['data']) && is_array($decResponse['data']) && empty($decResponse['data'])) {
                throw new Exception("No Streamer", ErrorCodes::STREAMERS_404);
            }
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::STREAMERS_500);
        }
        return json_decode($response, true)['data'];
    }
}
