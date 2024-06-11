<?php

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class GetTopsOfTheTopsTest extends TestCase
{
    protected ApiClient $apiClient;
    protected DBClient $dbClient;
    protected string $token;
    protected array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClient = Mockery::mock(ApiClient::class);
        $this->dbClient = Mockery::mock(DBClient::class);
        $this->token = 'token';
        $this->headers = ['Authorization: Bearer ' . $this->token];
        $this->app->instance(ApiClient::class, $this->apiClient);
        $this->app->instance(DBClient::class, $this->dbClient);
    }

    /**
     * @test
     */
    public function GetTOTTWithSince(): void
    {
        $this->dbClient->expects('getToken')->andReturn($this->token);
        $this->apiClient->expects('getTop3Games')->with($this->headers)->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $this->dbClient->expects('checkGameId')->andReturn(false);
        $this->apiClient->expects('getTop40Videos')->with(1, $this->headers)->andReturn(json_encode(['data' => ['video1']]));
        $this->dbClient->expects('getAndInsertGameTopsOfTheTops')->andReturn(['data']);

        $response = $this->get('/analytics/topsofthetops?since=10');

        $response->assertStatus(200);
        $response->assertJson([['data']]);
    }

    /**
     * @test
     */
    public function GetTOTTWithoutSince(): void
    {
        $this->dbClient->expects('getToken')->andReturn($this->token);
        $this->apiClient->expects('getTop3Games')->with($this->headers)->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $this->dbClient->expects('checkGameId')->andReturn(false);
        $this->apiClient->expects('getTop40Videos')->with(1, $this->headers)->andReturn(json_encode(['data' => ['video1']]));
        $this->dbClient->expects('getAndInsertGameTopsOfTheTops')->andReturn(['data']);

        $response = $this->get('/analytics/topsofthetops');

        $response->assertStatus(200);
        $response->assertJson([['data']]);
    }

    /**
     * @test
     */
    public function ErrorIfFailInGettingToken(): void
    {
        $this->dbClient->expects('getToken')->andThrow(new Exception('Error', ErrorCodes::TOKEN_500));

        $response = $this->get('/analytics/topsofthetops');

        $response->assertStatus(503);
        $response->assertJson(['error' => 'No se puede establecer conexión con Twitch en este momento']);
    }

    /**
     * @test
     */
    public function ErrorIfFailInGettingTop3Games(): void
    {
        $this->dbClient->expects('getToken')->andReturn($this->token);
        $this->apiClient->expects('getTop3Games')->with($this->headers)->andThrow(new Exception('Error', ErrorCodes::TOP3GAMES_500));

        $response = $this->get('/analytics/topsofthetops');

        $response->assertStatus(503);
        $response->assertJson(['error' => 'No se pueden devolver los 3 mejores juegos en este momento, inténtalo más tarde']);
    }

    /**
     * @test
     */
    public function ErrorIfFailInGettingTop40Videos(): void
    {
        $this->dbClient->expects('getToken')->andReturn($this->token);
        $this->apiClient->expects('getTop3Games')->with($this->headers)->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $this->dbClient->expects('checkGameId')->andReturn(false);
        $this->apiClient->expects('getTop40Videos')->with(1, $this->headers)->andThrow(new Exception('Error', ErrorCodes::TOP40VIDEOS_500));

        $response = $this->get('/analytics/topsofthetops');

        $response->assertStatus(503);
        $response->assertJson(['error' => 'No se pueden devolver los 40 mejores videos en este momento, inténtalo más tarde']);
    }
}
