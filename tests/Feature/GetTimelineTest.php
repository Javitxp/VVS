<?php

use App\Services\TokenProvider;
use App\Services\UserDataProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class GetTimelineTest extends TestCase
{
    protected UserDataProvider $userDataProviderMock;
    protected TokenProvider $tokenProviderMock;
    protected string $userId;
    protected string $expectedToken;
    protected array $expectedTimeline;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userId = "1";
        $this->expectedToken = 'token';
        $this->expectedTimeline = [
            [
                "streamerId" => "streamer1",
                "streamerName" => "Streamer 1",
                "title" => "Stream 1",
                "game" => "Game 1",
                "viewerCount" => 100,
                "startedAt" => "2024-05-10T12:00:00Z"
            ],
            [
                "streamerId" => "streamer1",
                "streamerName" => "Streamer 1",
                "title" => "Stream 2",
                "game" => "Game 2",
                "viewerCount" => 100,
                "startedAt" => "2024-05-10T12:00:00Z"
            ],
            [
                "streamerId" => "streamer1",
                "streamerName" => "Streamer 1",
                "title" => "Stream 3",
                "game" => "Game 3",
                "viewerCount" => 100,
                "startedAt" => "2024-05-10T12:00:00Z"
            ],
            [
                "streamerId" => "streamer1",
                "streamerName" => "Streamer 1",
                "title" => "Stream 2",
                "game" => "Game 2",
                "viewerCount" => 100,
                "startedAt" => "2024-05-10T12:00:00Z"
            ],
            [
                "streamerId" => "streamer1",
                "streamerName" => "Streamer 1",
                "title" => "Stream 2",
                "game" => "Game 2",
                "viewerCount" => 100,
                "startedAt" => "2024-05-10T12:00:00Z"
            ],
        ];
        $this->userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->tokenProviderMock->expects('getToken')->andReturn($this->expectedToken);
        $this->app->instance(UserDataProvider::class, $this->userDataProviderMock);
        $this->app->instance(TokenProvider::class, $this->tokenProviderMock);
    }

    /**
     * @test
     */
    public function GetsTimeline()
    {
        $this->userDataProviderMock->expects('getUserFollowedStreamersTimeline')
            ->with($this->expectedToken, $this->userId)
            ->andReturn($this->expectedTimeline);

        $response = $this->get('/analytics/timeline/'.$this->userId);

        $response->assertStatus(200);
        $response->assertJson($this->expectedTimeline);
    }

    /**
     * @test
     */
    public function ReturnsFileNotFoundWhenCallingWithANonExistentUser()
    {
        $this->userDataProviderMock->expects('getUserFollowedStreamersTimeline')
            ->with($this->expectedToken, $this->userId)
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
        $this->userDataProviderMock->expects('getUserFollowedStreamersTimeline')
            ->with($this->expectedToken, $this->userId)
            ->andThrow(new Exception("Error al obtener los streams de Twitch.", ErrorCodes::TIMELINE_500));

        $response = $this->get('/analytics/timeline/'.$this->userId);

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Error al obtener los streams de Twitch.']);
    }
}
