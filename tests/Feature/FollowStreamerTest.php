<?php

use App\Models\RegistredUser;
use App\Services\StreamerDataProvider;
use App\Services\TokenProvider;
use App\Services\UserDataProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;

class FollowStreamerTest extends TestCase
{
    /**
     * @test
     */
    public function GetsUsers()
    {
        $userId = "1";
        $streamerId = "1";
        $token = 'token';
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $tokenProviderMock = Mockery::mock(TokenProvider::class);
        $strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $userDataProviderMock->expects('followStreamer')
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

        $response = $this->post('analytics/follow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Ahora sigues a '. $streamerId
        ]);
    }
    /**
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
        $userDataProviderMock->expects('followStreamer')
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

        $response = $this->post('analytics/follow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(404);
        $response->assertJson([
            "error" => "El usuario ( $userId ) o el streamer ( $streamerId ) especificados no existe en la API."
        ]);
    }
    /**
     * @test
     */
    public function WhenUserTriesToFollowAFollowedStreamerReturns409()
    {
        $userId = "1";
        $streamerId = "1";
        $token = 'token';
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $tokenProviderMock = Mockery::mock(TokenProvider::class);
        $strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $userDataProviderMock->expects('followStreamer')
            ->with($userId, $streamerId)
            ->andThrow(new Exception("Error", ErrorCodes::FOLLOW_409));
        $tokenProviderMock->expects('getToken')
            ->andReturn($token);
        $strDataProviderMock->expects('getStreamerData')
            ->with($token, $streamerId)
            ->andReturn(['data']);
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $this->app->instance(TokenProvider::class, $tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $strDataProviderMock);

        $response = $this->post('analytics/follow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(409);
        $response->assertJson([
            "error" => "409 Conflict : El usuario ya está siguiendo al streamer."
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
        $userDataProviderMock->expects('followStreamer')
            ->with($userId, $streamerId)
            ->andThrow(new Exception("Error", ErrorCodes::STREAMERS_500));
        $tokenProviderMock->expects('getToken')
            ->andReturn($token);
        $strDataProviderMock->expects('getStreamerData')
            ->with($token, $streamerId)
            ->andReturn(['data']);
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $this->app->instance(TokenProvider::class, $tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $strDataProviderMock);

        $response = $this->post('analytics/follow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(500);
        $response->assertJson([
            "error" => "No se pueden devolver streamers en este momento, inténtalo más tarde"
        ]);
    }
}
