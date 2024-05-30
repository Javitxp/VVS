<?php

namespace Tests\Feature;

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\StreamsDataManager;
use App\Services\StreamsDataProvider;
use App\Services\TokenProvider;
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
        $streamsDataManagerMock = $this->mock(StreamsDataManager::class);
        $streamsDataManagerMock->shouldReceive('getStreams')
            ->andThrow(new Exception("No se puede establecer conexión con Twitch en este momento", ErrorCodes::TOKEN_500));

        $response = $this->get('/analytics/streams');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se puede establecer conexión con Twitch en este momento"]);
    }

    /**
     * @test
     */
    public function ErrorIfFailInGettingStreams(): void
    {
        $streamsDataManagerMock = $this->mock(StreamsDataManager::class);
        $streamsDataManagerMock->shouldReceive('getStreams')
            ->andThrow(new Exception("No se pueden devolver streams en este momento, inténtalo más tarde", ErrorCodes::STREAMS_500));

        $response = $this->get('/analytics/streams');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver streams en este momento, inténtalo más tarde"]);
    }
}
