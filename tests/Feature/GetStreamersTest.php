<?php

namespace Tests\Feature;

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\StreamerDataManager;
use App\Utilities\ErrorCodes;
use Exception;
use Mockery;
use Tests\TestCase;

class GetStreamersTest extends TestCase
{
    /**
     * @test
     */
    public function GetsStreamers(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $getExpectedToken = 'token';
        $streamerId = 1;
        $dbClient->expects('getToken')->andReturn($getExpectedToken);
        $getStreamersExpectedResponse = json_encode(['data' => [['user_id' => '1', 'user_name' => 'streamer1']]]);
        $apiClient->expects('makeCurlCall')
            ->with('https://api.twitch.tv/helix/users?id='.$streamerId, ['Authorization: Bearer '. $getExpectedToken])
            ->andReturn($getStreamersExpectedResponse);
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/streamers?id='.$streamerId);

        $response->assertStatus(200);
        $response->assertJson([['user_id' => '1', 'user_name' => 'streamer1']]);
    }
    /**
     * @test
     */
    public function ErrorWhenFailInGettingToken(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $dbClient->expects('getToken')
            ->andThrow(new Exception("Error 500", ErrorCodes::TOKEN_500));
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/streamers?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se puede establecer conexión con Twitch en este momento"]);
    }
    /**
     * @test
     */
    public function ErrorWhenFailInGettingStreamers(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $token = 'token';
        $streamerId = 69;
        $dbClient->expects('getToken')
            ->andReturn($token);
        $headers = ['Authorization: Bearer '. $token];
        $apiClient->expects('makeCurlCall')
            ->with('https://api.twitch.tv/helix/users?id='.$streamerId, $headers)
            ->andThrow(new Exception("Error 500", ErrorCodes::STREAMERS_500));
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/streamers?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver streamers en este momento, inténtalo más tarde"]);
    }
}
