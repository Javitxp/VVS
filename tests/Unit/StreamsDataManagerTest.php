<?php

use App\Services\StreamsDataManager;
use App\Services\StreamsDataProvider;
use App\Services\TokenProvider;
use Tests\TestCase;

class StreamsDataManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->strDataProviderMock = Mockery::mock(StreamsDataProvider::class);
        $this->strDataManager = new StreamsDataManager($this->tokenProviderMock, $this->strDataProviderMock);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetStreams()
    {
        $expectedToken = "token_from_db";
        $expectedResponse = [ [
                'title' => 'title',
                'user_name' => 'user_name'
            ]
        ];
        $this->tokenProviderMock->expects('getToken')
            ->andReturn('token_from_db');
        $this->strDataProviderMock->expects('execute')
            ->with($expectedToken)
            ->andReturn($expectedResponse);

        $response = $this->strDataManager->getStreams();

        $this->assertEquals($expectedResponse, $response);
    }
}
