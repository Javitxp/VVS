<?php

use App\Infrastructure\Clients\DBClient;
use App\Models\RegistredUser;
use App\Services\StreamerDataProvider;
use App\Services\TokenProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class FollowStreamerTest extends TestCase
{
    protected DBClient $dbClientMock;
    protected TokenProvider $tokenProviderMock;
    protected StreamerDataProvider $strDataProviderMock;
    protected string $userId = "1";
    protected string $streamerId = "1";
    protected string $token = 'token';

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->strDataProviderMock = Mockery::mock(StreamerDataProvider::class);
        $this->app->instance(DBClient::class, $this->dbClientMock);
        $this->app->instance(TokenProvider::class, $this->tokenProviderMock);
        $this->app->instance(StreamerDataProvider::class, $this->strDataProviderMock);
        $this->tokenProviderMock->expects('getToken')->andReturn($this->token);
    }

    /**
     * @test
     */
    public function FollowsStreamer()
    {
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([]);
        $updatedUser = new RegistredUser();
        $updatedUser->followedStreamers = json_encode([$this->streamerId]);
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andReturn(['data']);
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andReturn($user);
        $this->dbClientMock->expects('updateUserFollowedStreamers')
            ->with($user, json_encode([$this->streamerId], JSON_UNESCAPED_SLASHES))
            ->andReturn($updatedUser);

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
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andThrows(new Exception("Error", ErrorCodes::STREAMERS_404));

        $response = $this->post('analytics/follow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(404);
        $response->assertJson([
            "error" => "El usuario ( " . $this->userId . " ) o el streamer ( " . $this->streamerId . " ) especificados no existe en la API.",
        ]);
    }
    /**
     * @test
     */
    public function WhenUserIsNotOnTheApiReturns404()
    {
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andReturn(['data']);
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andReturn(null);

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
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([$this->streamerId]);
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andReturn(['data']);
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andReturn($user);

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
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([]);
        $this->strDataProviderMock->expects('getStreamerData')
            ->with($this->token, $this->streamerId)
            ->andReturn(['data']);
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andReturn($user);
        $this->dbClientMock->expects('updateUserFollowedStreamers')
            ->with($user, json_encode([$this->streamerId], JSON_UNESCAPED_SLASHES))
            ->andThrows(new Exception("Error", ErrorCodes::STREAMERS_500));

        $response = $this->post('analytics/follow', ['userId' => $this->userId, 'streamerId' => $this->streamerId]);

        $response->assertStatus(500);
        $response->assertJson([
            "error" => "No se pueden devolver streamers en este momento, inténtalo más tarde"
        ]);
    }
}
