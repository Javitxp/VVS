<?php

namespace App\Services;
use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;


class TokenProvider
{

    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getToken()
    {
        $dbClient = new DBClient();
        $token = $dbClient->getToken();
        if($token != null){
            return $token;
        }
        $token = $this->apiClient->getToken();
        // TODO: Meter token en la DB
        return $token;
    }
}
