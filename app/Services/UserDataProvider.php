<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;
use App\Infrastructure\Clients\DBClient;
use App\Utilities\ErrorCodes;
use Exception;

class UserDataProvider
{
    private DBClient $dbClient;
    private ApiClient $apiClient;
    public function __construct(DBClient $dbClient, ApiClient $apiClient)
    {
        $this->dbClient = $dbClient;
        $this->apiClient = $apiClient;
    }

    /**
     * @throws Exception
     */
    public function createUser($username, $password)
    {
        if($this->dbClient->checkUsername($username)) {
            throw new Exception("El nombre de usuario ya está en uso.", ErrorCodes::USERS_409);
        }
        try {
            return $this->dbClient->insertUser($username, $password);
        } catch (Exception $exception) {
            throw new Exception("Error al crear el usuario.", ErrorCodes::USERS_500);
        }
    }
    /**
     * @throws Exception
     */
    public function getAllUsers()
    {
        try {
            return $this->dbClient->getAllUsers();
        } catch (Exception $exception) {
            throw new Exception("Error al obtener la lista de usuarios.", ErrorCodes::USERS_500);
        }
    }

    /**
     * @throws Exception
     */
    public function getUserFollowedStreamersTimeline($token, $userId)
    {
        $user = $this->dbClient->findUserById($userId);
        if (!$user) {
            throw new Exception("El usuario especificado no existe.", ErrorCodes::TIMELINE_404);
        }
        $followedStreamers = json_decode($user->followedStreamers);
        if (empty($followedStreamers)) {
            return [];
        }
        $streams = [];
        foreach (array_chunk($followedStreamers, 100) as $chunk) {
            $data = $this->apiClient->getStreamsFromStreamer($token, $chunk);
            foreach ($data as $stream) {
                $streams[] = [
                    'streamerId' => $stream['user_id'],
                    'streamerName' => $stream['user_name'],
                    'title' => $stream['title'],
                    'game' => $stream['game_name'],
                    'viewerCount' => $stream['viewer_count'],
                    'startedAt' => $stream['started_at']
                ];
            }
        }
        usort($streams, function ($first, $second) {
            return strtotime($second['startedAt']) - strtotime($first['startedAt']);
        });
        return array_slice($streams, 0, 5);
    }
    /**
     * @throws Exception
     */
    public function followStreamer($userId, $streamerId)
    {
        $user = $this->dbClient->findUserById($userId);
        if (!$user) {
            throw new Exception("El usuario especificado no existe.", ErrorCodes::USERS_404);
        }
        $followedStreamers = json_decode($user['followedStreamers'], true) ?? [];
        if (in_array($streamerId, $followedStreamers)) {
            throw new Exception("El usuario ya está siguiendo al streamer.", ErrorCodes::FOLLOW_409);
        }
        $followedStreamers[] = $streamerId;
        return $this->dbClient->updateUserFollowedStreamers($user, json_encode($followedStreamers, JSON_UNESCAPED_SLASHES));
    }

    /**
     * @throws Exception
     */
    public function unfollowStreamer($userId, $streamerId)
    {
        $user = $this->dbClient->findUserById($userId);
        if (!$user) {
            throw new Exception("El usuario especificado no existe.", ErrorCodes::USERS_404);
        }
        $followedStreamers = json_decode($user['followedStreamers'], true) ?? [];
        if (!in_array($streamerId, $followedStreamers)) {
            throw new Exception("El usuario no está siguiendo al streamer", 500);
        }
        $followedStreamers = array_values(array_filter($followedStreamers, function ($followedId) use ($streamerId) {
            return $followedId !== $streamerId;
        }));
        return $this->dbClient->updateUserFollowedStreamers($user, json_encode($followedStreamers, JSON_UNESCAPED_SLASHES));
    }
}
