<?php

namespace App\Services;

use App\Infrastructure\Clients\DBClient;
use App\Models\RegistredUser;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class UserDataProvider
{
    private DBClient $dbClient;
    public function __construct(DBClient $dbClient)
    {
        $this->dbClient = $dbClient;
    }

    /**
     * @throws Exception
     */
    public function createUser($username, $password)
    {
        // Verificar si el usuario ya existe
        if($this->dbClient->checkUsername($username)) {
            throw new Exception("El nombre de usuario ya está en uso.", ErrorCodes::USERS_409);
        }

        try {
            $user = new RegistredUser();
            $user->username = $username;
            $user->password = Hash::make($password);
            $user->followedStreamers = json_encode([]);
            $user->save();

            return $user;
        } catch (Exception $e) {
            throw new Exception("Error al crear el usuario.", ErrorCodes::USERS_500);
        }
    }
    /**
     * @throws Exception
     */
    public function getAllUsers()
    {
        try {
            $users = RegistredUser::select('username', DB::raw('JSON_UNQUOTE(followedStreamers) as followedStreamers'))->get();
            return $users;
        } catch (Exception $e) {
            throw new Exception("Error al obtener la lista de usuarios.", ErrorCodes::USERS_500);
        }
    }

    /**
     * @throws Exception
     */
    public function getUserFollowedStreamersTimeline($token, $userId)
    {
        $user = RegistredUser::find($userId);

        if (!$user) {
            throw new Exception("El usuario especificado no existe.", ErrorCodes::TIMELINE_404);
        }

        $followedStreamers = json_decode($user->followedStreamers);

        if (empty($followedStreamers)) {
            return [];
        }

        $clientId = ENV('CLIENT_ID');

        $streams = [];

        foreach (array_chunk($followedStreamers, 100) as $chunk) {
            $response = Http::withHeaders([
                'Client-Id' => $clientId,
                'Authorization' => "Bearer $token",
            ])->get('https://api.twitch.tv/helix/streams', [
                'user_id' => $chunk,
            ]);

            if ($response->failed()) {
                throw new Exception("Error al obtener los streams de Twitch.", ErrorCodes::TIMELINE_500);
            }

            $data = $response->json('data');
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
        // Verificar si el usuario ya existe

        if (RegistredUser::where('id', $userId)->exists()) {
            try {
                $user = RegistredUser::where('id', $userId)->firstOrFail();

                $followedStreamers = json_decode($user->followedStreamers, true) ?? [];

                if (in_array($streamerId, $followedStreamers)) {
                    throw new Exception("409 Conflict : El usuario ya está siguiendo al streamer.", ErrorCodes::FOLLOW_409);
                }

                $followedStreamers[] = $streamerId;
                $user->followedStreamers = json_encode($followedStreamers, JSON_UNESCAPED_SLASHES);

                $user->save();
                return $user;

            } catch (Exception $e) {
                throw new Exception("409 Conflict : El usuario ya está siguiendo al streamer.", ErrorCodes::FOLLOW_409);
            }
        } else {
            throw new Exception("El usuario (  ) NO EXISTE", ErrorCodes::USERS_404);
        }
    }
    public function unfollowStreamer($userId, $streamerId)
    {
        if (!RegistredUser::where('id', $userId)->exists()) {
            throw new Exception("El usuario ( " . $userId . " ) NO EXISTE", ErrorCodes::USERS_404);
        }
        try {
            $user = RegistredUser::where('id', $userId)->firstOrFail();
            $followedStreamers = json_decode($user->followedStreamers, true) ?? [];

            if (!in_array($streamerId, $followedStreamers)) {
                throw new Exception("El usuario no está siguiendo al streamer", 500);
            }

            $followedStreamers = array_filter($followedStreamers, function ($followedId) use ($streamerId) {
                return $followedId !== $streamerId;
            });

            $user->followedStreamers = json_encode($followedStreamers, JSON_UNESCAPED_SLASHES);
            $user->save();
            return $user;
        } catch (Exception $e) {
            throw new Exception("Error del servidor al dejar de seguir al streamer.", 500);
        }
    }
}
