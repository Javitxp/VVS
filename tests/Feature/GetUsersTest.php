<?php

use App\Services\UserDataProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class GetUsersTest extends TestCase
{
    protected UserDataProvider $userDataProviderMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $this->app->instance(UserDataProvider::class, $this->userDataProviderMock);
    }

    /**
     * @test
     */
    public function GetsUsers()
    {
        $this->userDataProviderMock->expects('getAllUsers')->andReturn([
            "username" => "somename",
            "followedStreamers" => ["streamer1", "streamer2"]
        ]);

        $response = $this->get('/analytics/users');

        $response->assertStatus(200);
        $response->assertJson([
            "username" => "somename",
            "followedStreamers" => ["streamer1", "streamer2"]
        ]);
    }

    /**
     * @test
     */
    public function ReturnsServerErrorWhenFailInGetUsers()
    {
        $this->userDataProviderMock->expects('getAllUsers')
            ->andThrow(new Exception("Error al obtener la lista de usuarios.", ErrorCodes::USERS_500));

        $response = $this->get('/analytics/users');

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Error del servidor al obtener la lista de usuarios.']);
    }
}
