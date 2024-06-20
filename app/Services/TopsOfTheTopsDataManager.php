<?php

namespace App\Services;

class TopsOfTheTopsDataManager
{
    private TokenProvider $tokenProvider;
    private TopsOfTheTopsDataProvider $topsDataProvider;
    public function __construct(TokenProvider $tokenProvider, TopsOfTheTopsDataProvider $topsDataProvider)
    {
        $this->tokenProvider = $tokenProvider;
        $this->topsDataProvider = $topsDataProvider;
    }

    /**
     * @throws \Exception
     */
    public function getTopsOfTheTops($since)
    {
        return $this->topsDataProvider->execute($this->tokenProvider->getToken(), $since);
    }
}
