<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\StreamsDataManager;
use App\Services\StreamsDataProvider;
use App\Services\TokenProvider;
use App\Services\StreamerDataManager;
use App\Services\StreamerDataProvider;
use App\Utilities\ErrorCodes;
use Mockery;
use Exception;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function analyticsStreamersReturns500WithoutId(): void
    {
        $response = $this->get('/analytics/streamers');

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Parameter id required']);
    }

    /**
     * @test
     */
    public function analyticsStreamersReturns500WithNonNumericId(): void
    {
        $response = $this->get('/analytics/streamers?id=abcde');

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Parameter id must be a number']);
    }

    /**
     * @test
     */
    public function analyticsStreamersReturnSuccessfulResponseWithNumericId(): void
    {
        $userDataProvider = Mockery::mock(StreamerDataProvider::class);
        $tokenProvider = Mockery::mock(TokenProvider::class);

        $this->app
            ->when(StreamerDataManager::class)
            ->needs(StreamerDataProvider::class)
            ->give(fn() => $userDataProvider);

        $this->app
            ->when(StreamerDataManager::class)
            ->needs(TokenProvider::class)
            ->give(fn() => $tokenProvider);

        $getExpectedToken = 'token';

        $getUsersExpectedResponse = json_encode(['user_id' => 'id', 'user_name' => 'user_name']);

        $tokenProvider->expects('getToken')->andReturn($getExpectedToken);

        $userDataProvider->expects('getStreamerData')->with($getExpectedToken)->andReturn($getUsersExpectedResponse);
        $userDataProvider->expects('setStreamerId')->withArgs(["20"]);

        $response = $this->get('/analytics/streamers?id=20');

        $response->assertStatus(200);
        $response->assertContent('"{\"user_id\":\"id\",\"user_name\":\"user_name\"}"');
    }

    /**
     * @test
     */
    public function analyticsStreamsReturnSuccessfulResponse(): void
    {
        $streamsDataProvider = Mockery::mock(StreamsDataProvider::class);
        $tokenProvider = Mockery::mock(TokenProvider::class);

        $this->app
            ->when(StreamsDataManager::class)
            ->needs(StreamsDataProvider::class)
            ->give(fn() => $streamsDataProvider);

        $this->app
            ->when(StreamsDataManager::class)
            ->needs(TokenProvider::class)
            ->give(fn() => $tokenProvider);

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
    public function analyticsStreamsReturnsErrorMessageWithCode503WhenFailInGettingToken(): void
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
    public function analyticsStreamsReturnsErrorMessageWithCode503WhenFailInGettingStreams(): void
    {
        $streamsDataManagerMock = $this->mock(StreamsDataManager::class);

        $streamsDataManagerMock->shouldReceive('getStreams')
            ->andThrow(new Exception("No se pueden devolver streams en este momento, inténtalo más tarde", ErrorCodes::STREAMS_500));

        $response = $this->get('/analytics/streams');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver streams en este momento, inténtalo más tarde"]);
    }

    /**
     * @test
     */
    public function analyticsStreamersReturnsErrorMessageWithCode503WhenFailInGettingToken(): void
    {
        $userDataManagerMock = $this->mock(StreamerDataManager::class);

        $userDataManagerMock->shouldReceive('setStreamerId')->with('69')->once();

        $userDataManagerMock->shouldReceive('getStreamerData')
            ->andThrow(new Exception("No se puede establecer conexión con Twitch en este momento", ErrorCodes::TOKEN_500));

        $response = $this->get('/analytics/streamers?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se puede establecer conexión con Twitch en este momento"]);
    }
    /**
     * @test
     */
    public function analyticsStreamersReturnsErrorMessageWithCode503WhenFailInGettingStreamers(): void
    {
        $userDataManagerMock = $this->mock(StreamerDataManager::class);

        $userDataManagerMock->shouldReceive('setStreamerId')->with('69')->once();

        $userDataManagerMock->shouldReceive('getStreamerData')
            ->andThrow(new Exception("No se pueden devolver streamers en este momento, inténtalo más tarde", ErrorCodes::STREAMERS_500));

        $response = $this->get('/analytics/streamers?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver streamers en este momento, inténtalo más tarde"]);
    }
}
