<?php

use App\Models\RegistredUser;
use App\Services\StreamerDataProvider;
use App\Services\TokenProvider;
use App\Services\UserDataProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class FollowStreamerTest extends TestCase
{
    protected UserDataProvider $userDataProviderMock;
    protected TokenProvider $tokenProviderMock;
    protected StreamerDataProvider $strDataProviderMock;
    protected string $userId = "1";
    protected string $streamerId = "1";
    protected string $token = 'token';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $this->app->instance(UserDataProvider::class, $this->userDataProviderMock);
        $this->app->instance(TokenProvider::class, $this->tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $this->strDataProviderMock);
        $this->tokenProviderMock->expects('getToken')->andReturn($this->token);
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andReturn(['data']);
    }

    /**
     * @test
     */
    public function GetsUsers()
    {
        $this->userDataProviderMock->expects('followStreamer')
            ->with($this->userId, $this->streamerId)
            ->andReturn(new RegistredUser());

        $response = $this->post('analytics/follow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Ahora sigues a '. $this->streamerId
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

        $response = $this->post('analytics/follow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(404);
        $response->assertJson([
            "error" => "El usuario ( " . $userId . " ) o el streamer ( " . $streamerId . " ) especificados no existe en la API.",
        ]);
    }
    /**
     * @test
     */
    public function WhenUserIsNotOnTheApiReturns404()
    {
        $this->userDataProviderMock->expects('followStreamer')
            ->with($this->userId, $this->streamerId)
            ->andThrow(new Exception("Error", ErrorCodes::USERS_404));

        $response = $this->post('analytics/follow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(404);
        $response->assertJson([
            "error" => "El usuario ( $this->userId ) o el streamer ( $this->streamerId ) especificados no existe en la API."
        ]);
    }

    /**
     * @test
     */
    public function WhenUserTriesToFollowAFollowedStreamerReturns409()
    {
        $this->userDataProviderMock->expects('followStreamer')
            ->with($this->userId, $this->streamerId)
            ->andThrow(new Exception("Error", ErrorCodes::FOLLOW_409));

        $response = $this->post('analytics/follow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

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
        $this->userDataProviderMock->expects('followStreamer')
            ->with($this->userId, $this->streamerId)
            ->andThrow(new Exception("Error", ErrorCodes::STREAMERS_500));

        $response = $this->post('analytics/follow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(500);
        $response->assertJson([
            "error" => "No se pueden devolver streamers en este momento, inténtalo más tarde"
        ]);
    }
}
