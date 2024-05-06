<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;

class CurlManager
{
    private $token;
    private ApiClient $apiClient;
    function __construct(ApiClient $apiClient){
        $this->apiClient = $apiClient;
        $this->token = $this->getToken();
    }

    private function getToken(): String{
        $url = 'https://id.twitch.tv/oauth2/token';

        $response = $this->apiClient->getToken($url);

        $result = json_decode($response);

        if(isset($result->access_token)){
            $this->token = $result->access_token;
        }

        return $this->token;
    }

    public function getCurlResponse(String $url): String{
        $headers = array('Authorization: Bearer '.$this->token);

        $response = $this->apiClient->makeCurlCall($url, $headers);

        return $response;
    }
}
