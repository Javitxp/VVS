<?php

use App\Services\StreamerDataManager;
use App\Services\StreamerDataProvider;
use App\Services\TokenProvider;
use Tests\TestCase;

class StreamerDataManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $this->strDataManager = new StreamerDataManager($this->tokenProviderMock, $this->strDataProviderMock);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetStreams()
    {
        $expectedToken = "token_from_db";
        $streamerId = "1";
        $expectedResponse = 'streamer_data';
        $this->tokenProviderMock->expects('getToken')
            ->andReturn('token_from_db');
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($expectedToken, $streamerId)
            ->andReturn($expectedResponse);

        $response = $this->strDataManager->getStreamerData($streamerId);

        $this->assertEquals($expectedResponse, $response);
    }
}
