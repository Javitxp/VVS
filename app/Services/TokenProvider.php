<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;

class TokenProvider
{
    private $apiClient;
    private $dbClient;

    public function __construct(ApiClient $apiClient, DBClient $dbClient)
    {
        $this->apiClient = $apiClient;
        $this->dbClient = $dbClient;
    }

    public function getToken()
    {
        $token = $this->dbClient->getToken();
        if($token != null) {
            return $token;
        }
        $token = $this->apiClient->getToken();
        if($token == null) {
            return null;
        }
        $this->dbClient->replaceToken($token);
        return $token;
    }
}
