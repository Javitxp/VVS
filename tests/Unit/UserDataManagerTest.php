<?php

use App\Models\RegistredUser;
use App\Services\TokenProvider;
use App\Services\UserDataManager;
use App\Services\UserDataProvider;
use Tests\TestCase;

class UserDataManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenProviderMock = Mockery::mock(TokenProvider::class);
        $this->userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $this->userDataManager = new UserDataManager($this->userDataProviderMock, $this->tokenProviderMock);
    }

    /**
     * @test
     * @throws Exception
     */
    public function CreatesUser()
    {
        $username = "somename";
        $password = "password";
        $user = new RegistredUser();
        $this->userDataProviderMock->expects('createUser')
            ->with($username, $password)
            ->andReturn($user);

        $newUser = $this->userDataManager->createUser($username, $password);

        $this->assertEquals($newUser, $user);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetsUserList()
    {
        $userList = [
            "username" => "somename",
            "followedStreamers" => ["streamer1", "streamer2"]
        ];
        $this->userDataProviderMock->expects('getAllUsers')
            ->andReturn($userList);

        $newUserList = $this->userDataManager->getAllUsers();

        $this->assertEquals($newUserList, $userList);
    }
    /**
     * @test
     * @throws Exception
     */
    public function GetsTimeline()
    {
        $expectedToken = 'token';
        $userId = "1";
        $timeline = [
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
        ];
        $this->tokenProviderMock->expects('getToken')
            ->andReturn($expectedToken);
        $this->userDataProviderMock->expects('getUserFollowedStreamersTimeline')
            ->with($expectedToken, $userId)
            ->andReturn($timeline);

        $newTimeline = $this->userDataManager->getUserFollowedStreamersTimeline($userId);

        $this->assertEquals($newTimeline, $timeline);
    }
    /**
     * @test
     * @throws Exception
     */
    public function FollowsStreamer()
    {
        $userId = "1";
        $streamerId = "1";
        $user = new RegistredUser();
        $this->userDataProviderMock->expects('followStreamer')
            ->with($userId, $streamerId)
            ->andReturn($user);

        $newUser = $this->userDataManager->followStreamer($userId, $streamerId);

        $this->assertEquals($newUser, $user);
    }
    /**
     * @test
     * @throws Exception
     */
    public function UnfollowsStreamer()
    {
        $userId = "1";
        $streamerId = "1";
        $user = new RegistredUser();
        $this->userDataProviderMock->expects('unfollowStreamer')
            ->with($userId, $streamerId)
            ->andReturn($user);

        $newUser = $this->userDataManager->unfollowStreamer($userId, $streamerId);

        $this->assertEquals($newUser, $user);
    }
}
