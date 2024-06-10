<?php
use App\Models\RegistredUser;
use App\Services\UserDataProvider;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserDataProviderTest extends TestCase
{
    private $userDataProviderMock;
    private $userDataManager;
    private $tokenProviderMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registredUserMock = $this->mock(RegistredUser::class);
        $this->hashMock = $this->mock(Hash::class);

        $this->userDataProvider = new UserDataProvider();

    }


    /**
     * @test
     * @throws Exception
     */
    public function createsUser()
    {
        $username = "newuser";
        $expectedUsername = "newuser";
        $password = "password";
        $expectedPassword = "hashedPassword";
        $expectedFollowedStreamers = json_encode([]);
        $this->registredUserMock->expects("where")
            ->with("username", $username)
            ->andReturnSelf();
        $this->registredUserMock->expects("exists")
            ->andReturn(false);
        $this->registredUserMock->expects("save")
            ->andReturn(true);
        $this->hashMock->expects("make")
            ->with($password)
            ->andReturn($expectedPassword);

        $result = $this->userDataProvider->createUser($username, $password);

        $this->assertEquals($expectedUsername, $result->username);
        $this->assertEquals($expectedPassword, $result->password);
        $this->assertEquals($expectedFollowedStreamers, $result->followedStreamers);
    }

    /**
     * @test
     * @throws Exception
     */
    public function getAllUsers()
    {
        // TODO
    }

    /**
     * @test
     * @throws Exception
     */
    public function getUserFollowedStreamersTimeline()
    {
        // TODO
    }

    /**
     * @test
     * @throws Exception
     */
    public function followsStreamer()
    {
        // TODO
    }

    /**
     * @test
     * @throws Exception
     */
    public function unfollowsStreamer()
    {
        // TODO
    }

}