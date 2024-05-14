<?php

namespace Tests\Feature;
use App\Services\TokenProvider;
use App\Services\UserDataManager;
use App\Services\UserDataProvider;
use App\Utilities\ErrorCodes;
use Exception;
use Mockery;
use Tests\TestCase;
class GetUsersTest extends TestCase
{
    /**
     * @test
     */
    public function ErrorIfNoIdGivenInUsers(): void
    {
        $response = $this->get('/analytics/users');

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Parameter id required']);
    }

    /**
     * @test
     */
    public function ErrorIfNoNumericIdGivenInUsers(): void
    {
        $response = $this->get('/analytics/users?id=abcde');

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Parameter id must be a number']);
    }

    /**
     * @test
     */
    public function GetsUsers(): void
    {
        $userDataProvider = Mockery::mock(UserDataProvider::class);
        $tokenProvider = Mockery::mock(TokenProvider::class);

        $this->app
            ->when(UserDataManager::class)
            ->needs(UserDataProvider::class)
            ->give(fn () => $userDataProvider);

        $this->app
            ->when(UserDataManager::class)
            ->needs(TokenProvider::class)
            ->give(fn () => $tokenProvider);

        $getExpectedToken = 'token';

        $getUsersExpectedResponse = json_encode(['user_id' => 'id', 'user_name' => 'user_name']);

        $tokenProvider->expects('getToken')->andReturn($getExpectedToken);

        $userDataProvider->expects('getUserData')->with($getExpectedToken, "20")->andReturn($getUsersExpectedResponse);

        $response = $this->get('/analytics/users?id=20');

        $response->assertStatus(200);
        $response->assertContent('"{\"user_id\":\"id\",\"user_name\":\"user_name\"}"');
    }
    /**
     * @test
     */
    public function ErrorWhenFailInGettingToken(): void
    {
        $userDataManagerMock = $this->mock(UserDataManager::class);
        $userDataManagerMock->shouldReceive('getUserData')
            ->andThrow(new Exception("No se puede establecer conexión con Twitch en este momento", ErrorCodes::TOKEN_500));

        $response = $this->get('/analytics/users?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se puede establecer conexión con Twitch en este momento"]);
    }
    /**
     * @test
     */
    public function ErrorWhenFailInGettingUsers(): void
    {
        $userDataManagerMock = $this->mock(UserDataManager::class);
        $userDataManagerMock->shouldReceive('getUserData')
            ->andThrow(new Exception("No se pueden devolver usuarios en este momento, inténtalo más tarde", ErrorCodes::USERS_500));

        $response = $this->get('/analytics/users?id=69');

        $response->assertStatus(503);
        $response->assertJson(['error' => "No se pueden devolver usuarios en este momento, inténtalo más tarde"]);
    }
}
