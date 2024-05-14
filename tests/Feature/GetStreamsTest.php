<?php

namespace Tests\Feature;

use App\Infrastructure\Clients\ApiClient;
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
        $streamsDataProvider = Mockery::mock(StreamsDataProvider::class);
        $tokenProvider = Mockery::mock(TokenProvider::class);

        $this->app
            ->when(StreamsDataManager::class)
            ->needs(StreamsDataProvider::class)
            ->give(fn () => $streamsDataProvider);

        $this->app
            ->when(StreamsDataManager::class)
            ->needs(TokenProvider::class)
            ->give(fn () => $tokenProvider);

        $getExpectedToken = 'token';
        $tokenProvider->expects('getToken')->andReturn($getExpectedToken);
        $getStreamsExpectedResponse = json_encode(['title' => 'title', 'user_name' => 'user_name']);
        $streamsDataProvider->expects('execute')->with($getExpectedToken)->andReturn($getStreamsExpectedResponse);

        $response = $this->get('/analytics/streams');

        $response->assertStatus(200);
        $response->assertContent('"{\"title\":\"title\",\"user_name\":\"user_name\"}"');
    }
    /**
     * @test
     */
//    public function GetsStreams2(): void
//    {
//        $apiClient = Mockery::mock(ApiClient::class);
//        $this->app
//            ->when(StreamsDataProvider::class)
//            ->needs(ApiClient::class)
//            ->give(fn () => $apiClient);
//
//        $getExpectedToken = 'token';
//        $apiClient->expects('getToken')->andReturn($getExpectedToken);
//        $getStreamsExpectedResponse = json_encode(['title' => 'title', 'user_name' => 'user_name']);
//        $apiClient->expects('makeCurlCall')->with('https://api.twitch.tv/helix/streams', [0 => 'Authorization: Bearer token'])->andReturn($getStreamsExpectedResponse);
//
//        $response = $this->get('/analytics/streams');
//
//        $response->assertStatus(200);
//        $response->assertContent('"{\"title\":\"title\",\"user_name\":\"user_name\"}"');
//    }

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
