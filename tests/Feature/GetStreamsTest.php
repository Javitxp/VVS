<?php

namespace Tests\Feature;

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Utilities\ErrorCodes;
use Exception;
use Mockery;
use Tests\TestCase;

class GetStreamsTest extends TestCase
{
    protected ApiClient $apiClientMock;
    protected DBClient $dbClientMock;
    protected string $getExpectedToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->getExpectedToken = 'token';
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->app->instance(ApiClient::class, $this->apiClientMock);
        $this->app->instance(DBClient::class, $this->dbClientMock);
    }

    /**
     * @test
     */
    public function GetsStreams(): void
    {
        $this->dbClientMock->expects('getToken')->andReturn($this->getExpectedToken);
        $getStreamsExpectedResponse = json_encode(['data' => [['title' => 'title', 'user_name' => 'user_name']]]);
        $this->apiClientMock->expects('makeCurlCall')
            ->with('https://api.twitch.tv/helix/streams', ['Authorization: Bearer '. $this->getExpectedToken])
            ->andReturn($getStreamsExpectedResponse);

        $response = $this->get('/analytics/streams');

        $response->assertStatus(200);
        $response->assertJson([['title' => 'title', 'user_name' => 'user_name']]);
    }

    /**
     * @test
     */
    public function ErrorIfFailInGettingToken(): void
    {
        $this->dbClientMock->expects('getToken')->andThrow(new Exception("Error 500", ErrorCodes::TOKEN_500));

        $response = $this->get('/analytics/streams');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se puede establecer conexión con Twitch en este momento"]);
    }

    /**
     * @test
     */
    public function ErrorIfFailInGettingStreams(): void
    {
        $this->dbClientMock->expects('getToken')->andReturn($this->getExpectedToken);
        $headers = ['Authorization: Bearer '. $this->getExpectedToken];
        $this->apiClientMock->expects('makeCurlCall')
            ->with('https://api.twitch.tv/helix/streams', $headers)
            ->andThrow(new Exception("Error 500", ErrorCodes::STREAMS_500));

        $response = $this->get('/analytics/streams');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver streams en este momento, inténtalo más tarde"]);
    }
}
