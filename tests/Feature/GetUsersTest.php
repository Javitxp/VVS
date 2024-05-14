<?php

namespace Tests\Feature;
use App\Services\TokenProvider;
use App\Services\StreamerDataManager;
use App\Services\StreamerDataProvider;
use App\Utilities\ErrorCodes;
use Exception;
use Mockery;
use Tests\TestCase;
class GetUsersTest extends TestCase
{
    /**
     * @test
     */
    public function ErrorIfNoIdGivenInStreamers(): void
    {
        $response = $this->get('/analytics/streamers');

        $response->assertStatus(500);
        $response->assertJson(['message' => 'The id field is required.']);
    }

    /**
     * @test
     */
    public function ErrorIfNoNumericIdGivenInStreamers(): void
    {
        $response = $this->get('/analytics/streamers?id=abcde');

        $response->assertStatus(500);
        $response->assertJson(['message' => 'The id field must be a number.']);
    }

    /**
     * @test
     */
    public function GetsStreamers(): void
    {
        $streamerDataProvider = Mockery::mock(StreamerDataProvider::class);
        $tokenProvider = Mockery::mock(TokenProvider::class);

        $this->app
            ->when(StreamerDataManager::class)
            ->needs(StreamerDataProvider::class)
            ->give(fn () => $streamerDataProvider);

        $this->app
            ->when(StreamerDataManager::class)
            ->needs(TokenProvider::class)
            ->give(fn () => $tokenProvider);

        $getExpectedToken = 'token';

        $getUsersExpectedResponse = json_encode(['user_id' => 'id', 'user_name' => 'user_name']);

        $tokenProvider->expects('getToken')->andReturn($getExpectedToken);

        $streamerDataProvider->expects('getStreamerData')->with($getExpectedToken, "20")->andReturn($getUsersExpectedResponse);

        $response = $this->get('/analytics/streamers?id=20');

        $response->assertStatus(200);
        $response->assertContent('"{\"user_id\":\"id\",\"user_name\":\"user_name\"}"');
    }
    /**
     * @test
     */
    public function ErrorWhenFailInGettingToken(): void
    {
        $userDataManagerMock = $this->mock(StreamerDataManager::class);
        $userDataManagerMock->shouldReceive('getStreamerData')
            ->andThrow(new Exception("No se puede establecer conexión con Twitch en este momento", ErrorCodes::TOKEN_500));

        $response = $this->get('/analytics/streamers?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se puede establecer conexión con Twitch en este momento"]);
    }
    /**
     * @test
     */
    public function ErrorWhenFailInGettingStreamers(): void
    {
        $userDataManagerMock = $this->mock(StreamerDataManager::class);
        $userDataManagerMock->shouldReceive('getStreamerData')
            ->andThrow(new Exception("No se pueden devolver streamers en este momento, inténtalo más tarde", ErrorCodes::STREAMERS_500));

        $response = $this->get('/analytics/streamers?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver streamers en este momento, inténtalo más tarde"]);
    }
}
