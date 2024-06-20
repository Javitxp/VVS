<?php

use App\Infrastructure\Clients\DBClient;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class GetUsersTest extends TestCase
{
    protected DBClient $dbClientMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->app->instance(DBClient::class, $this->dbClientMock);
    }

    /**
     * @test
     */
    public function GetsUsers()
    {
        $this->dbClientMock->expects('getAllUsers')->andReturn([
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
        $this->dbClientMock->expects('getAllUsers')
            ->andThrow(new Exception("Error al obtener la lista de usuarios.", ErrorCodes::USERS_500));

        $response = $this->get('/analytics/users');

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Error del servidor al obtener la lista de usuarios.']);
    }
}
