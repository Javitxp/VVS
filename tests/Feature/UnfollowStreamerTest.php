<?php

use App\Models\RegistredUser;
use App\Services\UserDataProvider;
use App\Services\StreamerDataProvider;
use App\Services\TokenProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;

class UnfollowStreamerTest extends TestCase
{
    /**
     * @test
     */
    public function UnfollowSuccessful()
    {
        $userId = "1";
        $streamerId = "1";
        $token = 'token';
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $tokenProviderMock = Mockery::mock(TokenProvider::class);
        $strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $userDataProviderMock->expects('unfollowStreamer')
            ->with($userId, $streamerId)
            ->andReturn(new RegistredUser());
        $tokenProviderMock->expects('getToken')
            ->andReturn($token);
        $strDataProviderMock->expects('getStreamerData')
            ->with($token, $streamerId)
            ->andReturn(['data']);
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $this->app->instance(TokenProvider::class, $tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $strDataProviderMock);

        $response = $this->delete('analytics/unfollow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Dejaste de seguir a ' . $streamerId
        ]);
    }
    /**
     * @test
     */
    public function WhenStreamerIsNotOnTheApiReturns404()
    {
        $userId = "1";
        $streamerId = "1";
        $token = 'token';
        $tokenProviderMock = Mockery::mock(TokenProvider::class);
        $strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $tokenProviderMock->expects('getToken')
            ->andReturn($token);
        $strDataProviderMock->expects('getStreamerData')
            ->with($token, $streamerId)
            ->andThrow(new Exception("No streamer", ErrorCodes::STREAMERS_404));
        $this->app->instance(TokenProvider::class, $tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $strDataProviderMock);

        $response = $this->delete('analytics/unfollow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(404);
        $response->assertJson([
            "error" => "El usuario ( " . $userId . " ) o el streamer ( " . $streamerId . " ) especificado no existe en la API.",
        ]);
    }/**
     * @test
     */
    public function WhenUserIsNotOnTheApiReturns404()
    {
        $userId = "1";
        $streamerId = "1";
        $token = 'token';
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $tokenProviderMock = Mockery::mock(TokenProvider::class);
        $strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $userDataProviderMock->expects('unfollowStreamer')
            ->with($userId, $streamerId)
            ->andThrow(new Exception("Error", ErrorCodes::USERS_404));
        $tokenProviderMock->expects('getToken')
            ->andReturn($token);
        $strDataProviderMock->expects('getStreamerData')
            ->with($token, $streamerId)
            ->andReturn(['data']);
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $this->app->instance(TokenProvider::class, $tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $strDataProviderMock);

        $response = $this->delete('analytics/unfollow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(404);
        $response->assertJson([
            "error" => "El usuario ( " . $userId . " ) o el streamer ( " . $streamerId . " ) especificado no existe en la API.",
        ]);
    }
    /**
     * @test
     */
    public function WhenServerFailsReturns500()
    {
        $userId = "1";
        $streamerId = "1";
        $token = 'token';
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $tokenProviderMock = Mockery::mock(TokenProvider::class);
        $strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $userDataProviderMock->expects('unfollowStreamer')
            ->with($userId, $streamerId)
            ->andThrow(new Exception("Server Error", 500));
        $tokenProviderMock->expects('getToken')
            ->andReturn($token);
        $strDataProviderMock->expects('getStreamerData')
            ->with($token, $streamerId)
            ->andReturn(['data']);
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $this->app->instance(TokenProvider::class, $tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $strDataProviderMock);

        $response = $this->delete('analytics/unfollow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(500);
        $response->assertJson([
            "error" => "Internal Server Error : Error del servidor al dejar de seguir al streamer.",
        ]);
    }
}
