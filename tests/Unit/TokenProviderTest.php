<?php

namespace Tests\Unit;

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TokenProvider;
use Mockery;
use Tests\TestCase;

class TokenProviderTest extends TestCase
{

    private ApiClient $apiClientMock;
    private DBClient $dbClientMock;
    private TokenProvider $tokenProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->tokenProvider = new TokenProvider($this->apiClientMock, $this->dbClientMock);
    }

    /**
     * @test
     */
    public function testGetTokenFromDB()
    {
        $expectedToken = "token_from_db";

        $this->dbClientMock->expects('getToken')
            ->andReturn('token_from_db');

        $token = $this->tokenProvider->getToken();

        $this->assertEquals($expectedToken, $token);
    }

    /**
     * @test
     */
    public function testGetTokenFromAPI()
    {
        $expectedToken = "token_from_api";

        $this->dbClientMock->expects('getToken')
            ->andReturn(null);
        $this->apiClientMock->expects('getToken')
            ->andReturn('token_from_api');
        $this->dbClientMock->expects('replaceToken')
            ->andReturn(true);

        $token = $this->tokenProvider->getToken();

        $this->assertEquals($expectedToken, $token);
    }

}
