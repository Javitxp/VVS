<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;

class TopsOfTheTopsDataProvider
{
    private ApiClient $apiClient;
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function execute($token)
    {

    }

}
