<?php

namespace App\Services;
use App\Infrastructure\Clients\ApiClient;


class TokenProvider
{

    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getToken()
    {
        // TODO: Revisar si el token esta en la base de datos
        return $this->apiClient->getToken();
    }
}
