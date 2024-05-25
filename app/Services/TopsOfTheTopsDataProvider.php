<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Utilities\ErrorCodes;
use Exception;

class TopsOfTheTopsDataProvider
{
    private ApiClient $apiClient;
    private DBClient $dbClient;
    public function __construct(ApiClient $apiClient, DBClient $dbClient)
    {
        $this->apiClient = $apiClient;
        $this->dbClient = $dbClient;
    }

    /**
     * @throws Exception
     */
    public function execute($token, $since): array
    {
        $headers = array('Authorization: Bearer '. $token);
        try {
            $top3_games = $this->apiClient->getTop3Games($headers);
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::TOP3GAMES_500);
        }
        $topsOfTheTops = [];
        foreach ($top3_games as $game) {
            $game_id = $game["id"];
            $name = $game["name"];
            $result = $this->dbClient->checkGameId($game_id);
            try {
                $top40Videos = $this->apiClient->getTop40Videos($game_id, $headers);
            } catch (Exception $e) {
                throw new Exception("Error: Code 500", ErrorCodes::TOP40VIDEOS_500);
            }
            if ($result) {
                $json = isset($since) ? $this->dbClient->getSince($since, $game_id) : $this->dbClient->getLast10($game_id);
                if ($json === null) {
                    $json = $this->dbClient->updateGameTopsOfTheTops($game_id, $name, $top40Videos);
                }
            } else {
                $json = $this->dbClient->getAndInsertGameTopsOfTheTops($game_id, $name, $top40Videos);
            }
            $topsOfTheTops[] = $json;
        }
        return $topsOfTheTops;
    }
}
