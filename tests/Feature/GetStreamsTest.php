<?php

namespace Tests\Feature;

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\StreamsDataManager;
use App\Utilities\ErrorCodes;
use Exception;
use Mockery;
use Tests\TestCase;

class GetStreamsTest extends TestCase
{
    /**
     * @test
     */
    public function GetsStreams(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $getExpectedToken = 'token';
        $dbClient->expects('getToken')->andReturn($getExpectedToken);
        $getStreamsExpectedResponse = json_encode(['data' => [['title' => 'title', 'user_name' => 'user_name']]]);
        $apiClient->expects('makeCurlCall')
            ->with('https://api.twitch.tv/helix/streams', ['Authorization: Bearer '. $getExpectedToken])
            ->andReturn($getStreamsExpectedResponse);
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/streams');

        $response->assertStatus(200);
        $response->assertJson([['title' => 'title', 'user_name' => 'user_name']]);
    }

    /**
     * @test
     */
    public function ErrorIfFailInGettingToken(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $dbClient->expects('getToken')->andThrow(new Exception("Error 500", ErrorCodes::TOKEN_500));
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/streams');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se puede establecer conexión con Twitch en este momento"]);
    }

    /**
     * @test
     */
    public function ErrorIfFailInGettingStreams(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $token = 'token';
        $dbClient->expects('getToken')
            ->andReturn($token);
        $headers = ['Authorization: Bearer '. $token];
        $apiClient->expects('makeCurlCall')
            ->with('https://api.twitch.tv/helix/streams', $headers)
            ->andThrow(new Exception("Error 500", ErrorCodes::STREAMS_500));
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/streams');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver streams en este momento, inténtalo más tarde"]);
    }
}
