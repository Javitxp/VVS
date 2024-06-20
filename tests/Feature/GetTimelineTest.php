<?php

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Models\RegistredUser;
use App\Services\TokenProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class GetTimelineTest extends TestCase
{
    protected ApiClient $apiClientMock;
    protected DBClient $dbClientMock;
    protected TokenProvider $tokenProviderMock;
    protected string $userId;
    protected string $expectedToken;
    protected array $expectedTimeline;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userId = "1";
        $this->expectedToken = 'token';
        $this->streamData = [
            [
                'user_id' => '1',
                'user_name' => 'Streamer1',
                'title' => 'Stream1',
                'game_name' => 'Game1',
                'viewer_count' => 100,
                'started_at' => '2024-06-13T12:00:00Z',
            ],
            [
                'user_id' => '2',
                'user_name' => 'Streamer2',
                'title' => 'Stream2',
                'game_name' => 'Game2',
                'viewer_count' => 200,
                'started_at' => '2024-06-13T13:00:00Z',
            ],
            [
                'user_id' => '3',
                'user_name' => 'Streamer3',
                'title' => 'Stream3',
                'game_name' => 'Game3',
                'viewer_count' => 300,
                'started_at' => '2024-06-13T14:00:00Z',
            ],
        ];
        $this->expectedTimeline = [
            [
                'streamerId' => '3',
                'streamerName' => 'Streamer3',
                'title' => 'Stream3',
                'game' => 'Game3',
                'viewerCount' => 300,
                'startedAt' => '2024-06-13T14:00:00Z',
            ],
            [
                'streamerId' => '2',
                'streamerName' => 'Streamer2',
                'title' => 'Stream2',
                'game' => 'Game2',
                'viewerCount' => 200,
                'startedAt' => '2024-06-13T13:00:00Z',
            ],
            [
                'streamerId' => '1',
                'streamerName' => 'Streamer1',
                'title' => 'Stream1',
                'game' => 'Game1',
                'viewerCount' => 100,
                'startedAt' => '2024-06-13T12:00:00Z',
            ],
        ];
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->tokenProviderMock->expects('getToken')->andReturn($this->expectedToken);
        $this->app->instance(ApiClient::class, $this->apiClientMock);
        $this->app->instance(DBClient::class, $this->dbClientMock);
        $this->app->instance(TokenProvider::class, $this->tokenProviderMock);
    }

    /**
     * @test
     */
    public function GetsTimeline()
    {
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([1, 2, 3]);
        $this->dbClientMock->shouldReceive('findUserById')->with($this->userId)->andReturn($user);
        $this->apiClientMock->shouldReceive('getStreamsFromStreamer')
            ->with($this->expectedToken, Mockery::subset([1, 2, 3]))
            ->andReturn($this->streamData);

        $response = $this->get('/analytics/timeline/' . $this->userId);

        $response->assertStatus(200);
        $response->assertJson($this->expectedTimeline);
    }

    /**
     * @test
     */
    public function ReturnsFileNotFoundWhenCallingWithANonExistentUser()
    {
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andThrow(new Exception("El usuario especificado no existe.", ErrorCodes::TIMELINE_404));

        $response = $this->get('/analytics/timeline/'.$this->userId);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'El usuario especificado no existe.']);
    }
    /**
     * @test
     */
    public function ReturnsServerErrorWhenCallFails()
    {
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([1]);
        $this->dbClientMock->expects('findUserById')
            ->with($this->userId)
            ->andReturn($user);
        $this->apiClientMock->expects('getStreamsFromStreamer')
            ->with($this->expectedToken, [1])
            ->andThrows(new Exception("Error al obtener los streams de Twitch.", ErrorCodes::TIMELINE_500));

        $response = $this->get('/analytics/timeline/' . $this->userId);

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Error al obtener los streams de Twitch.']);
    }
}
