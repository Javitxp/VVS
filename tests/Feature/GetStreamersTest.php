<?php

namespace Tests\Feature;

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Utilities\ErrorCodes;
use Exception;
use Mockery;
use Tests\TestCase;

class GetStreamersTest extends TestCase
{
    protected ApiClient $apiClientMock;
    protected DBClient $dbClientMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->app->instance(ApiClient::class, $this->apiClientMock);
        $this->app->instance(DBClient::class, $this->dbClientMock);
    }
    /**
     * @test
     */
    public function GetsStreamers(): void
    {
        $getExpectedToken = 'token';
        $streamerId = 1;
        $getStreamersExpectedResponse = json_encode(['data' => [['user_id' => '1', 'user_name' => 'streamer1']]]);
        $this->dbClientMock->expects('getToken')->andReturn($getExpectedToken);
        $this->apiClientMock->expects('makeCurlCall')
            ->with('https://api.twitch.tv/helix/users?id='.$streamerId, ['Authorization: Bearer '. $getExpectedToken])
            ->andReturn($getStreamersExpectedResponse);

        $response = $this->get('/analytics/streamers?id='.$streamerId);

        $response->assertStatus(200);
        $response->assertJson([['user_id' => '1', 'user_name' => 'streamer1']]);
    }
    /**
     * @test
     */
    public function ErrorWhenFailInGettingToken(): void
    {
        $this->dbClientMock->expects('getToken')
            ->andThrow(new Exception("Error 500", ErrorCodes::TOKEN_500));

        $response = $this->get('/analytics/streamers?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se puede establecer conexión con Twitch en este momento"]);
    }
    /**
     * @test
     */
    public function ErrorWhenFailInGettingStreamers(): void
    {
        $token = 'token';
        $streamerId = 69;
        $headers = ['Authorization: Bearer '. $token];
        $this->dbClientMock->expects('getToken')
            ->andReturn($token);
        $this->apiClientMock->expects('makeCurlCall')
            ->with('https://api.twitch.tv/helix/users?id='.$streamerId, $headers)
            ->andThrow(new Exception("Error 500", ErrorCodes::STREAMERS_500));

        $response = $this->get('/analytics/streamers?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver streamers en este momento, inténtalo más tarde"]);
    }
}
