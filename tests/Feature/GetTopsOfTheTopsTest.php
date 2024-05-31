<?php

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class GetTopsOfTheTopsTest extends TestCase
{
    /**
     * @test
     */
    public function GetTOTTWithSince(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $token = 'token';
        $headers = ['Authorization: Bearer '. $token];
        $dbClient->expects('getToken')
            ->andReturn($token);
        $apiClient->expects('getTop3Games')
            ->with($headers)
            ->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $dbClient->expects('checkGameId')
            ->andReturn(false);
        $apiClient->expects('getTop40Videos')
            ->with(1, $headers)->andReturn(json_encode(['data' => ['video1']]));
        $dbClient->expects('getAndInsertGameTopsOfTheTops')
            ->andReturn(['data']);
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/topsofthetops?since=10');

        $response->assertStatus(200);
        $response->assertJson([['data']]);
    }
    /**
     * @test
     */
    public function GetTOTTWithoutSince(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $token = 'token';
        $headers = ['Authorization: Bearer '. $token];
        $dbClient->expects('getToken')
            ->andReturn($token);
        $apiClient->expects('getTop3Games')
            ->with($headers)
            ->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $dbClient->expects('checkGameId')
            ->andReturn(false);
        $apiClient->expects('getTop40Videos')
            ->with(1, $headers)->andReturn(json_encode(['data' => ['video1']]));
        $dbClient->expects('getAndInsertGameTopsOfTheTops')
            ->andReturn(['data']);
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/topsofthetops');

        $response->assertStatus(200);
        $response->assertJson([['data']]);
    }
    /**
     * @test
     */
    public function ErrorIfFailInGettingToken(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $dbClient->expects('getToken')
            ->andThrow(new Exception('Error', ErrorCodes::TOKEN_500));
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/topsofthetops');

        $response->assertStatus(503);
        $response->assertJson(['error' => 'No se puede establecer conexión con Twitch en este momento']);
    }
    /**
     * @test
     */
    public function ErrorIfFailInGettingTop3Games(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $token = 'token';
        $headers = ['Authorization: Bearer '. $token];
        $dbClient->expects('getToken')
            ->andReturn($token);
        $apiClient->expects('getTop3Games')
            ->with($headers)
            ->andThrow(new Exception('Error', ErrorCodes::TOP3GAMES_500));
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/topsofthetops');

        $response->assertStatus(503);
        $response->assertJson(['error' => 'No se pueden devolver los 3 mejores juegos en este momento, inténtalo más tarde']);
    }
    /**
     * @test
     */
    public function ErrorIfFailInGettingTop40Videos(): void
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $dbClient = Mockery::mock(DBClient::class);
        $token = 'token';
        $headers = ['Authorization: Bearer '. $token];
        $dbClient->expects('getToken')
            ->andReturn($token);
        $apiClient->expects('getTop3Games')
            ->with($headers)
            ->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $dbClient->expects('checkGameId')
            ->andReturn(false);
        $apiClient->expects('getTop40Videos')
            ->with(1, $headers)
            ->andThrow(new Exception('Error', ErrorCodes::TOP40VIDEOS_500));
        $this->app->instance(ApiClient::class, $apiClient);
        $this->app->instance(DBClient::class, $dbClient);

        $response = $this->get('/analytics/topsofthetops');

        $response->assertStatus(503);
        $response->assertJson(['error' => 'No se pueden devolver los 40 mejores videos en este momento, inténtalo más tarde']);
    }
}
