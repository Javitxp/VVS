<?php

use App\Infrastructure\Clients\DBClient;
use App\Models\RegistredUser;
use App\Services\StreamerDataProvider;
use App\Services\TokenProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;

class UnfollowStreamerTest extends TestCase
{
    protected DBClient $dbClientMock;
    protected TokenProvider $tokenProviderMock;
    protected StreamerDataProvider $strDataProviderMock;
    protected string $userId;
    protected string $streamerId;
    protected string $token;
    protected RegistredUser $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userId = "1";
        $this->streamerId = "1";
        $this->token = 'token';
        $this->user = new RegistredUser();
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $this->tokenProviderMock->expects('getToken')
            ->andReturn($this->token);
        $this->app->instance(DBClient::class, $this->dbClientMock);
        $this->app->instance(TokenProvider::class, $this->tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $this->strDataProviderMock);
    }

    /**
     * @test
     */
    public function UnfollowSuccessful()
    {
        $this->user->followedStreamers = json_encode([$this->streamerId]);
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andReturn($this->user);
        $this->dbClientMock->expects('updateUserFollowedStreamers')
            ->with($this->user, json_encode([], JSON_UNESCAPED_SLASHES))
            ->andReturn(new RegistredUser());
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
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andReturn(null);
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
    public function WhenUserDoesntFollowTheStreamerReturnsServerError()
    {
        $this->user->followedStreamers = json_encode([]);
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andReturn($this->user);
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andReturn(['data']);

        $response = $this->delete('analytics/unfollow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(500);
        $response->assertJson([
            "error" => "Internal Server Error : Error del servidor al dejar de seguir al streamer.",
        ]);
    }

    /**
     * @test
     */
    public function WhenServerFailsReturns500()
    {
        $this->user->followedStreamers = json_encode([$this->streamerId]);
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andReturn($this->user);
        $this->dbClientMock->expects('updateUserFollowedStreamers')
            ->with($this->user, json_encode([], JSON_UNESCAPED_SLASHES))
            ->andThrow(new Exception("Server Error", 500));
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
