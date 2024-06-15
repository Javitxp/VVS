<?php

use App\Services\TokenProvider;
use App\Services\TopsOfTheTopsDataManager;
use App\Services\TopsOfTheTopsDataProvider;
use Tests\TestCase;

class TopsOfTheTopsDataManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->tottDataProviderMock = Mockery::mock(TopsOfTheTopsDataProvider::class);
        $this->tottDataManager = new TopsOfTheTopsDataManager($this->tokenProviderMock, $this->tottDataProviderMock);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetsTOTTWithoutSince(): void
    {
        $this->tokenProviderMock->expects('getToken')
            ->andReturn('token');
        $this->tottDataProviderMock->expects('execute')
            ->with('token', '')
            ->andReturn(['data']);

        $result = $this->tottDataManager->getTopsOfTheTops(null);

        $this->assertEquals(['data'], $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function GetsTOTTWithSince(): void
    {
        $this->tokenProviderMock->expects('getToken')
            ->andReturn('token');
        $this->tottDataProviderMock->expects('execute')
            ->with('token', '2')
            ->andReturn(['data']);

        $result = $this->tottDataManager->getTopsOfTheTops('2');

        $this->assertEquals(['data'], $result);
    }
}
