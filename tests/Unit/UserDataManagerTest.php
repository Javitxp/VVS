<?php

use App\Models\RegistredUser;
use App\Services\UserDataManager;
use App\Services\UserDataProvider;
use Tests\TestCase;

class UserDataManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $this->userDataManager = new UserDataManager($this->userDataProviderMock);
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
}
