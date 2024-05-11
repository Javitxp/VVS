<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\StreamsDataManager;
use App\Services\StreamsDataProvider;
use App\Services\TokenProvider;
use App\Services\UserDataManager;
use App\Services\UserDataProvider;
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
    public function analyticsUsersReturns500WithoutId(): void
    {
        $response = $this->get('/analytics/users');

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Parameter id required']);
    }

    /**
     * @test
     */
    public function analyticsUsersReturns500WithNonNumericId(): void
    {
        $response = $this->get('/analytics/users?id=abcde');

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Parameter id must be a number']);
    }

    /**
     * @test
     */
    public function analyticsUsersReturnSuccessfulResponseWithNumericId(): void
    {
        $userDataProvider = Mockery::mock(UserDataProvider::class);
        $tokenProvider = Mockery::mock(TokenProvider::class);

        $this->app
            ->when(UserDataManager::class)
            ->needs(UserDataProvider::class)
            ->give(fn() => $userDataProvider);

        $this->app
            ->when(UserDataManager::class)
            ->needs(TokenProvider::class)
            ->give(fn() => $tokenProvider);

        $getExpectedToken = 'token';

        $getUsersExpectedResponse = json_encode(['user_id' => 'id', 'user_name' => 'user_name']);

        $tokenProvider->expects('getToken')->andReturn($getExpectedToken);

        $userDataProvider->expects('getUserData')->with($getExpectedToken)->andReturn($getUsersExpectedResponse);
        $userDataProvider->expects('setUserId')->withArgs(["20"]);

        $response = $this->get('/analytics/users?id=20');

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
    public function analyticsUsersReturnsErrorMessageWithCode503WhenFailInGettingToken(): void
    {
        $userDataManagerMock = $this->mock(UserDataManager::class);

        $userDataManagerMock->shouldReceive('setUserId')->with('69')->once();

        $userDataManagerMock->shouldReceive('getUserData')
            ->andThrow(new Exception("No se puede establecer conexión con Twitch en este momento", ErrorCodes::TOKEN_500));

        $response = $this->get('/analytics/users?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se puede establecer conexión con Twitch en este momento"]);
    }
    /**
     * @test
     */
    public function analyticsUsersReturnsErrorMessageWithCode503WhenFailInGettingUsers(): void
    {
        $userDataManagerMock = $this->mock(UserDataManager::class);

        $userDataManagerMock->shouldReceive('setUserId')->with('69')->once();

        $userDataManagerMock->shouldReceive('getUserData')
            ->andThrow(new Exception("No se pueden devolver usuarios en este momento, inténtalo más tarde", ErrorCodes::USERS_500));

        $response = $this->get('/analytics/users?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver usuarios en este momento, inténtalo más tarde"]);
    }
}
