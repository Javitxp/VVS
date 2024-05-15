<?php

use App\Services\TokenProvider;
use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use Mockery;
use Tests\TestCase;

class GetTokenTest extends TestCase
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
        $this->dbClientMock->expects('getToken')
                     ->andReturn('token_from_db');
        $token = $this->tokenProvider->getToken();
        $this->assertEquals('token_from_db', $token);
    }
    
    /**
     * @test
     */
    public function testGetTokenFromAPI()
    {
        $this->dbClientMock->expects('getToken')
                     ->andReturn(null);
        $this->apiClientMock->expects('getToken')
                      ->andReturn('token_from_api');
        $this->dbClientMock->expects('replaceToken')
                      ->andReturn(true);
        $token = $this->tokenProvider->getToken();
        $expectedToken = "token_from_api";	
        $this->assertEquals($expectedToken, $token);
    }

}