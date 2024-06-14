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
    protected UserDataProvider $userDataProviderMock;
    protected TokenProvider $tokenProviderMock;
    protected StreamerDataProvider $strDataProviderMock;
    protected string $userId;
    protected string $streamerId;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userId = "1";
        $this->streamerId = "1";
        $this->token = 'token';
        $this->userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $this->app->instance(UserDataProvider::class, $this->userDataProviderMock);
        $this->app->instance(TokenProvider::class, $this->tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $this->strDataProviderMock);
    }

    /**
     * @test
     */
    public function UnfollowSuccessful()
    {
        $this->userDataProviderMock->expects('unfollowStreamer')
            ->with($this->userId, $this->streamerId)
            ->andReturn(new RegistredUser());
        $this->tokenProviderMock->expects('getToken')
            ->andReturn($this->token);
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andReturn(['data']);

        $response = $this->delete('analytics/unfollow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Dejaste de seguir a ' . $this->streamerId
        ]);
    }

    /**
     * @test
     */
    public function WhenStreamerIsNotOnTheApiReturns404()
    {
        $this->tokenProviderMock->expects('getToken')
            ->andReturn($this->token);
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andThrow(new Exception("No streamer", ErrorCodes::STREAMERS_404));

        $response = $this->delete('analytics/unfollow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(404);
        $response->assertJson([
            "error" => "El usuario ( " . $this->userId . " ) o el streamer ( " . $this->streamerId . " ) especificado no existe en la API.",
        ]);
    }

    /**
     * @test
     */
    public function WhenUserIsNotOnTheApiReturns404()
    {
        $this->userDataProviderMock->expects('unfollowStreamer')
            ->with($this->userId, $this->streamerId)
            ->andThrow(new Exception("Error", ErrorCodes::USERS_404));
        $this->tokenProviderMock->expects('getToken')
            ->andReturn($this->token);
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andReturn(['data']);

        $response = $this->delete('analytics/unfollow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(404);
        $response->assertJson([
            "error" => "El usuario ( " . $this->userId . " ) o el streamer ( " . $this->streamerId . " ) especificado no existe en la API.",
        ]);
    }

    /**
     * @test
     */
    public function WhenServerFailsReturns500()
    {
        $this->userDataProviderMock->expects('unfollowStreamer')
            ->with($this->userId, $this->streamerId)
            ->andThrow(new Exception("Server Error", 500));
        $this->tokenProviderMock->expects('getToken')
            ->andReturn($this->token);
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andReturn(['data']);

        $response = $this->delete('analytics/unfollow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(500);
        $response->assertJson([
            "error" => "Internal Server Error : Error del servidor al dejar de seguir al streamer.",
        ]);
    }
}
