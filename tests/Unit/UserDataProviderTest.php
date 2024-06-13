<?php

use App\Infrastructure\Clients\DBClient;
use App\Services\UserDataProvider;
use Tests\TestCase;

class UserDataProviderTest extends TestCase
{

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
        $dbClient->expects('checkUsername')
            ->with($username)
            ->andReturn(false);
        $dbClient->expects('insertUser')
            ->with($username,$expectedPassword, $followedStreamers)
            ->andReturn(true);
        Hash::shouldReceive('make')->with($password)->andReturn($expectedPassword);

        $userDataProvider = new UserDataProvider($dbClient);
        $user = $userDataProvider->createUser($username, $password);

        $this->assertEquals($username, $user->username);
        $this->assertEquals($expectedPassword, $user->password);
        $this->assertEquals($followedStreamers, $user->followedStreamers);
        
    }

}
