<?php

namespace App\Services;

class TopsOfTheTopsDataManager
{
    private TokenProvider $tokenProvider;
    private TopsOfTheTopsDataProvider $topsOfTheTopsDataProvider;
    public function __construct(TokenProvider $tokenProvider, TopsOfTheTopsDataProvider $topsOfTheTopsDataProvider)
    {
        $this->tokenProvider = $tokenProvider;
        $this->topsOfTheTopsDataProvider = $topsOfTheTopsDataProvider;
    }

    public function getTopsOfTheTops(){
        $response = $this->topsOfTheTopsDataProvider->execute($this->tokenProvider->getToken());
        return $response;
    }
}
