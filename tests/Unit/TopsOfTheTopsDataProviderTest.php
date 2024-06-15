<?php

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TopsOfTheTopsDataProvider;
use Tests\TestCase;

class TopsOfTheTopsDataProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->tottDataProvider = new TopsOfTheTopsDataProvider($this->apiClientMock, $this->dbClientMock);
    }

    /**
     * @test
     * @throws Exception
     */
    public function WhenSinceNotGivenItExecutes(): void
    {
        $this->apiClientMock->expects('getTop3Games')
            ->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $this->dbClientMock->expects('checkGameId')
            ->andReturn(false);
        $this->apiClientMock->expects('getTop40Videos')
            ->andReturn(json_encode(['data' => ['video1']]));
        $this->dbClientMock->expects('getAndInsertGameTopsOfTheTops')
            ->andReturn(['data']);

        $result = $this->tottDataProvider->execute('token', null);

        $this->assertEquals([['data']], $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function WhenSinceGivenItExecutes(): void
    {
        $this->apiClientMock->expects('getTop3Games')
            ->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $this->dbClientMock->expects('checkGameId')
            ->andReturn(false);
        $this->apiClientMock->expects('getTop40Videos')
            ->andReturn(json_encode(['data' => ['video1']]));
        $this->dbClientMock->expects('getAndInsertGameTopsOfTheTops')
            ->andReturn(['data']);

        $result = $this->tottDataProvider->execute('token', '2');

        $this->assertEquals([['data']], $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function WhenGameIsInDBAndSinceIsGivenItExecutes(): void
    {
        $this->apiClientMock->expects('getTop3Games')
            ->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $this->dbClientMock->expects('checkGameId')
            ->andReturn(true);
        $this->apiClientMock->expects('getTop40Videos')
            ->andReturn(json_encode(['data' => ['video1']]));
        $this->dbClientMock->expects('getSince')
            ->andReturn(['data']);

        $result = $this->tottDataProvider->execute('token', '2');

        $this->assertEquals([['data']], $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function WhenGameIsInDBAndSinceIsNotGivenGetsGamesFromLast10Min(): void
    {
        $this->apiClientMock->expects('getTop3Games')
            ->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $this->dbClientMock->expects('checkGameId')
            ->andReturn(true);
        $this->apiClientMock->expects('getTop40Videos')
            ->andReturn(json_encode(['data' => ['video1']]));
        $this->dbClientMock->expects('getLast10')
            ->andReturn(['data']);

        $result = $this->tottDataProvider->execute('token', null);

        $this->assertEquals([['data']], $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function WhenGameIsInDBSinceIsGivenAndDoesntFindGamesItUpdatesTheGame(): void
    {
        $this->apiClientMock->expects('getTop3Games')
            ->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $this->dbClientMock->expects('checkGameId')
            ->andReturn(true);
        $this->apiClientMock->expects('getTop40Videos')
            ->andReturn(json_encode(['data' => ['video1']]));
        $this->dbClientMock->expects('getSince')
            ->andReturn(null);
        $this->dbClientMock->expects('updateGameTopsOfTheTops')
            ->andReturn(['data']);

        $result = $this->tottDataProvider->execute('token', '2');

        $this->assertEquals([['data']], $result);
    }
    /**
     * @test
     * @throws Exception
     */
    public function WhenGameIsInDBSinceIsNotGivenAndDoesntFindGamesInLast10MinItUpdatesTheGame(): void
    {
        $this->apiClientMock->expects('getTop3Games')
            ->andReturn(json_encode(['data' => [['id' => 1, 'name' => 'game1']]]));
        $this->dbClientMock->expects('checkGameId')
            ->andReturn(true);
        $this->apiClientMock->expects('getTop40Videos')
            ->andReturn(json_encode(['data' => ['video1']]));
        $this->dbClientMock->expects('getLast10')
            ->andReturn(null);
        $this->dbClientMock->expects('updateGameTopsOfTheTops')
            ->andReturn(['data']);

        $result = $this->tottDataProvider->execute('token', null);

        $this->assertEquals([['data']], $result);
    }
}
