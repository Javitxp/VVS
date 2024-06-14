<?php

use App\Models\RegistredUser;
use PHPUnit\Framework\TestCase;
use Mockery;
use App\Services\UserDataProvider;
use App\Infrastructure\Clients\DBClient;
use App\Infrastructure\Clients\ApiClient;
use App\Utilities\ErrorCodes;
use Exception;

class UserDataProviderTest extends TestCase
{
    private DBClient $dbClientMock;
    private ApiClient $apiClientMock;
    private UserDataProvider $userDataProvider;

    protected function setUp(): void
    {
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->userDataProvider = new UserDataProvider($this->dbClientMock, $this->apiClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
    /**
     * @test
     * @throws Exception
     */
    public function CreatesUser()
    {
        $username = 'testuser';
        $password = 'password123';
        $this->dbClientMock->expects('checkUsername')
            ->with($username)
            ->andReturn(false);
        $this->dbClientMock->expects('insertUser')
            ->with($username, $password)
            ->andReturn(new RegistredUser());

        $result = $this->userDataProvider->createUser($username, $password);

        $this->assertEquals($result, new RegistredUser());
    }
    /**
     * @test
     * @throws Exception
     */
    public function FailsWhenUsernameExists()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("El nombre de usuario ya está en uso.");
        $this->expectExceptionCode(ErrorCodes::USERS_409);
        $username = 'testuser';
        $password = 'password123';
        $this->dbClientMock->expects('checkUsername')
            ->with($username)
            ->andReturn(true);

        $this->userDataProvider->createUser($username, $password);
    }
    /**
     * @test
     * @throws Exception
     */
    public function FailsCreatingUserWhenThereAreDBErrors()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error al crear el usuario.");
        $this->expectExceptionCode(ErrorCodes::USERS_500);
        $username = 'testuser';
        $password = 'password123';
        $this->dbClientMock->expects('checkUsername')
            ->with($username)
            ->andReturn(false);
        $this->dbClientMock->expects('insertUser')
            ->with($username, $password)
            ->andThrow(new Exception());

        $this->userDataProvider->createUser($username, $password);
    }
    /**
     * @test
     * @throws Exception
     */
    public function GetsUsers()
    {
        $users = [['id' => 1, 'username' => 'testuser']];
        $this->dbClientMock->expects('getAllUsers')
            ->andReturn($users);

        $result = $this->userDataProvider->getAllUsers();

        $this->assertEquals($users, $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function FailsGettingUsersWhenDBError()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error al obtener la lista de usuarios.");
        $this->expectExceptionCode(ErrorCodes::USERS_500);
        $this->dbClientMock->expects('getAllUsers')
            ->andThrow(new Exception());

        $this->userDataProvider->getAllUsers();
    }
    /**
     * @test
     * @throws Exception
     */
    public function GetsTimeline()
    {
        $userId = 1;
        $token = 'test-token';
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([1, 2, 3]);
        $streamData = [
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
        $expectedResponse = [
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
        $this->dbClientMock->expects('findUserById')
            ->with($userId)
            ->andReturn($user);
        $this->apiClientMock->expects('getStreamsFromStreamer')
            ->with($token, Mockery::subset([1, 2, 3]))
            ->andReturn($streamData);

        $result = $this->userDataProvider->getUserFollowedStreamersTimeline($token, $userId);

        $this->assertEquals($expectedResponse, $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function FailsGettingTimelineWhenUserNotFound()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("El usuario especificado no existe.");
        $this->expectExceptionCode(ErrorCodes::TIMELINE_404);
        $userId = 1;
        $token = 'test-token';
        $this->dbClientMock->expects('findUserById')
            ->with($userId)
            ->andReturn(null);

        $this->userDataProvider->getUserFollowedStreamersTimeline($token, $userId);
    }
    /**
     * @test
     * @throws Exception
     */
    public function GetsTimelineWithEmptyFollowedStreamers()
    {
        $userId = 1;
        $token = 'test-token';
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([]);
        $this->dbClientMock->expects('findUserById')
            ->with($userId)
            ->andReturn($user);

        $result = $this->userDataProvider->getUserFollowedStreamersTimeline($token, $userId);

        $this->assertEquals([], $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function FollowsStreamer()
    {
        $userId = 1;
        $streamerId = 2;
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([1]);
        $this->dbClientMock->expects('findUserById')->with($userId)->andReturn($user);
        $this->dbClientMock->expects('updateUserFollowedStreamers')
            ->with(Mockery::on(function ($argument) use ($user) {
                return $argument == $user;
            }), json_encode([1, $streamerId], JSON_UNESCAPED_SLASHES))
            ->andReturn($user);

        $result = $this->userDataProvider->followStreamer($userId, $streamerId);

        $this->assertEquals($user, $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function FailsInFollowingWhenUserNotFound()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("El usuario especificado no existe.");
        $this->expectExceptionCode(ErrorCodes::USERS_404);
        $userId = 1;
        $streamerId = 2;
        $this->dbClientMock->expects('findUserById')
            ->with($userId)
            ->andReturn(null);

        $this->userDataProvider->followStreamer($userId, $streamerId);
    }
    /**
     * @test
     * @throws Exception
     */
    public function FailsInFollowingWhenAlreadyFollowed()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("El usuario ya está siguiendo al streamer.");
        $this->expectExceptionCode(ErrorCodes::FOLLOW_409);
        $userId = 1;
        $streamerId = 2;
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([1, 2]);
        $this->dbClientMock->expects('findUserById')
            ->with($userId)
            ->andReturn($user);

        $this->userDataProvider->followStreamer($userId, $streamerId);
    }
    /**
     * @test
     * @throws Exception
     */
    public function UnfollowsStreamer()
    {
        $userId = 1;
        $streamerId = 2;
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([1, 2]);
        $this->dbClientMock->expects('findUserById')
            ->with($userId)
            ->andReturn($user);
        $this->dbClientMock->expects('updateUserFollowedStreamers')
            ->with(Mockery::on(function ($argument) use ($user) {
                return $argument == $user;
            }), json_encode([1], JSON_UNESCAPED_SLASHES))
            ->andReturn($user);

        $result = $this->userDataProvider->unfollowStreamer($userId, $streamerId);

        $this->assertEquals($user, $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function FailsUnfollowWhenUserNotFound()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("El usuario especificado no existe.");
        $this->expectExceptionCode(ErrorCodes::USERS_404);
        $userId = 1;
        $streamerId = 2;
        $this->dbClientMock->expects('findUserById')
            ->with($userId)
            ->andReturn(null);

        $this->userDataProvider->unfollowStreamer($userId, $streamerId);
    }
    /**
     * @test
     * @throws Exception
     */
    public function FailsUnfollowWhenNotFollowing()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("El usuario no está siguiendo al streamer");
        $this->expectExceptionCode(500);
        $userId = 1;
        $streamerId = 2;
        $user = new RegistredUser();
        $user->followedStreamers = json_encode([1]);
        $this->dbClientMock->expects('findUserById')
            ->with($userId)
            ->andReturn($user);

        $this->userDataProvider->unfollowStreamer($userId, $streamerId);
    }
}
