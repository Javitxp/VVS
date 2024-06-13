<?php

use App\Infrastructure\Clients\DBClient;
use App\Services\UserDataProvider;
use Tests\TestCase;

class UserDataProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function createsUserSuccessfully()
    {
        $username = "username";
        $password = "password";
        $expectedPassword = "hashedPassword";
        $followedStreamers = json_encode([]);
        $dbClient = Mockery::mock(DBClient::class);
        $dbClient->shouldReceive('checkUsername')
            ->with($username)
            ->andReturn(false);
        $dbClient->shouldReceive('insertUser')
            ->with($username, $expectedPassword, $followedStreamers)
            ->andReturn(true);
        Hash::shouldReceive('make')->with($password)->andReturn($expectedPassword);

        $userDataProvider = new UserDataProvider($dbClient);
        $user = $userDataProvider->createUser($username, $password);

        $this->assertEquals($username, $user->username);
        $this->assertEquals($expectedPassword, $user->password);
        $this->assertEquals($followedStreamers, $user->followedStreamers);
    }

    /**
     * @test
     */
    public function getAllUsersSuccessfully()
    {
        $usersArray = [
            (object)[
                'username' => 'user1',
                'followedStreamers' => '["streamer1", "streamer2"]'
            ],
            (object)[
                'username' => 'user2',
                'followedStreamers' => '["streamer3", "streamer4"]'
            ]
        ];

        $dbClient = Mockery::mock(DBClient::class);
        $dbClient->shouldReceive('getAllUsers')
            ->andReturn(collect($usersArray));

        $userDataProvider = new UserDataProvider($dbClient);
        $users = $userDataProvider->getAllUsers();

        $this->assertCount(2, $users);
        $this->assertEquals('user1', $users[0]->username);
        $this->assertEquals('["streamer1", "streamer2"]', $users[0]->followedStreamers);
        $this->assertEquals('user2', $users[1]->username);
        $this->assertEquals('["streamer3", "streamer4"]', $users[1]->followedStreamers);
    }

    /**
     * @test
     */
    public function followStreamerSuccessfully()
    {
        $userId = 1;
        $streamerId = 2;
        $user = [
            'id' => $userId,
            'username' => 'username',
            'password' => 'hashedPassword',
            'followedStreamers' => json_encode([1])
        ];

        $updatedUser = [
            'id' => $userId,
            'username' => 'username',
            'password' => 'hashedPassword',
            'followedStreamers' => json_encode([1, 2])
        ];

        $dbClient = Mockery::mock(DBClient::class);
        $dbClient->expects('findUserById')
            ->with($userId)
            ->andReturn($user);
        $dbClient->expects('updateUserFollowedStreamers')
            ->with($userId, json_encode([1, 2]))
            ->andReturn(true);

        $userDataProvider = new UserDataProvider($dbClient);
        $result = $userDataProvider->followStreamer($userId, $streamerId);

        $this->assertEquals($updatedUser['followedStreamers'], $result['followedStreamers']);
    }

    /**
     * @test
     */
    public function unfollowStreamerSuccessfully()
    {
        $userId = 1;
        $streamerId = 2;
        $user = [
            'id' => $userId,
            'username' => 'username',
            'password' => 'hashedPassword',
            'followedStreamers' => json_encode([1, 2, 3])
        ];

        $updatedFollowedStreamers = json_encode([1, 3], JSON_UNESCAPED_SLASHES);
        $updatedUser = [
            'id' => $userId,
            'username' => 'username',
            'password' => 'hashedPassword',
            'followedStreamers' => $updatedFollowedStreamers
        ];

        $dbClient = Mockery::mock(DBClient::class);
        $dbClient->expects('findUserById')
            ->with($userId)
            ->andReturn($user);
        $dbClient->expects('updateUserFollowedStreamers')
            ->with($userId, $updatedFollowedStreamers)
            ->andReturn(true);

        $userDataProvider = new UserDataProvider($dbClient);
        $result = $userDataProvider->unfollowStreamer($userId, $streamerId);

        $this->assertEquals($updatedUser['followedStreamers'], $result['followedStreamers']);
    }

}
