<?php

use App\Services\TokenProvider;
use App\Services\UserDataProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;

class GetTimelineTest extends TestCase
{
    /**
     * @test
     */
    public function GetsTimeline()
    {
        $userId = "1";
        $expectedToken = 'token';
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $tokenProviderMock = Mockery::mock(TokenProvider::class);
        $tokenProviderMock->expects('getToken')
            ->andReturn($expectedToken);
        $userDataProviderMock->expects('getUserFollowedStreamersTimeline')
            ->with($expectedToken, $userId)
            ->andReturn([
                0 =>
                [
                    "streamerId" => "streamer1",
                    "streamerName" => "Streamer 1",
                    "title" => "Stream 1",
                    "game" => "Game 1",
                    "viewerCount" => 100,
                    "startedAt" => "2024-05-10T12:00:00Z"
                ],
                1 =>
                    [
                        "streamerId" => "streamer1",
                        "streamerName" => "Streamer 1",
                        "title" => "Stream 2",
                        "game" => "Game 2",
                        "viewerCount" => 100,
                        "startedAt" => "2024-05-10T12:00:00Z"
                    ],
                2 =>
                    [
                        "streamerId" => "streamer1",
                        "streamerName" => "Streamer 1",
                        "title" => "Stream 3",
                        "game" => "Game 3",
                        "viewerCount" => 100,
                        "startedAt" => "2024-05-10T12:00:00Z"
                    ],
                3 =>
                    [
                        "streamerId" => "streamer1",
                        "streamerName" => "Streamer 1",
                        "title" => "Stream 2",
                        "game" => "Game 2",
                        "viewerCount" => 100,
                        "startedAt" => "2024-05-10T12:00:00Z"
                    ],
                4 =>
                    [
                        "streamerId" => "streamer1",
                        "streamerName" => "Streamer 1",
                        "title" => "Stream 2",
                        "game" => "Game 2",
                        "viewerCount" => 100,
                        "startedAt" => "2024-05-10T12:00:00Z"
                    ],
            ]);
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $this->app->instance(TokenProvider::class, $tokenProviderMock);

        $response = $this->get('/analytics/timeline/'.$userId);

        $response->assertStatus(200);
        $response->assertJson([
            0 =>
                [
                    "streamerId" => "streamer1",
                    "streamerName" => "Streamer 1",
                    "title" => "Stream 1",
                    "game" => "Game 1",
                    "viewerCount" => 100,
                    "startedAt" => "2024-05-10T12:00:00Z"
                ],
            1 =>
                [
                    "streamerId" => "streamer1",
                    "streamerName" => "Streamer 1",
                    "title" => "Stream 2",
                    "game" => "Game 2",
                    "viewerCount" => 100,
                    "startedAt" => "2024-05-10T12:00:00Z"
                ],
            2 =>
                [
                    "streamerId" => "streamer1",
                    "streamerName" => "Streamer 1",
                    "title" => "Stream 3",
                    "game" => "Game 3",
                    "viewerCount" => 100,
                    "startedAt" => "2024-05-10T12:00:00Z"
                ],
            3 =>
                [
                    "streamerId" => "streamer1",
                    "streamerName" => "Streamer 1",
                    "title" => "Stream 2",
                    "game" => "Game 2",
                    "viewerCount" => 100,
                    "startedAt" => "2024-05-10T12:00:00Z"
                ],
            4 =>
                [
                    "streamerId" => "streamer1",
                    "streamerName" => "Streamer 1",
                    "title" => "Stream 2",
                    "game" => "Game 2",
                    "viewerCount" => 100,
                    "startedAt" => "2024-05-10T12:00:00Z"
                ],
        ]);
    }
    /**
     * @test
     */
    public function ReturnsFileNotFoundWhenCallingWithANonExistentUser()
    {
        $userId = "1";
        $expectedToken = 'token';
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $tokenProviderMock = Mockery::mock(TokenProvider::class);
        $tokenProviderMock->expects('getToken')
            ->andReturn($expectedToken);
        $userDataProviderMock->expects('getUserFollowedStreamersTimeline')
            ->with($expectedToken, $userId)
            ->andThrow(new Exception("El usuario especificado no existe.", ErrorCodes::TIMELINE_404));
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $this->app->instance(TokenProvider::class, $tokenProviderMock);

        $response = $this->get('/analytics/timeline/'.$userId);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'El usuario especificado no existe.']);
    }
    /**
     * @test
     */
    public function ReturnsServerErrorWhenCallFails()
    {
        $userId = "1";
        $expectedToken = 'token';
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $tokenProviderMock = Mockery::mock(TokenProvider::class);
        $tokenProviderMock->expects('getToken')
            ->andReturn($expectedToken);
        $userDataProviderMock->expects('getUserFollowedStreamersTimeline')
            ->with($expectedToken, $userId)
            ->andThrow(new Exception("Error al obtener los streams de Twitch.", ErrorCodes::TIMELINE_500));
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $this->app->instance(TokenProvider::class, $tokenProviderMock);

        $response = $this->get('/analytics/timeline/'.$userId);

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Error al obtener los streams de Twitch.']);
    }
}
