<?php

use App\Services\UserDataProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;

class GetUsersTest extends TestCase
{
    /**
     * @test
     */
    public function GetsUsers()
    {
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $userDataProviderMock->expects('getAllUsers')->andReturn([
            "username" => "somename",
            "followedStreamers" => ["streamer1", "streamer2"]
        ]);
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);

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
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $userDataProviderMock->expects('getAllUsers')
            ->andThrow(new Exception("Error al obtener la lista de usuarios.", ErrorCodes::USERS_500));
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);

        $response = $this->get('/analytics/users');

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Error del servidor al obtener la lista de usuarios.']);
    }
}
