<?php

namespace App\Services;

use App\Models\RegistredUser;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserDataProvider
{
    /**
     * @throws Exception
     */
    public function createUser($username, $password)
    {
        // Verificar si el usuario ya existe
        if (RegistredUser::where('username', $username)->exists()) {
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
            return RegistredUser::all(['username', 'followedStreamers']);
        } catch (Exception $e) {
            throw new Exception("Error al obtener la lista de usuarios.", ErrorCodes::USERS_500);
        }
    }

    /**
     * @throws Exception
     */
    public function getUserFollowedStreamersTimeline($userId)
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
        $accessToken = ENV('ACCESS_TOKEN');

        $streams = [];

        foreach (array_chunk($followedStreamers, 100) as $chunk) {
            $response = Http::withHeaders([
                'Client-Id' => $clientId,
                'Authorization' => "Bearer $accessToken",
            ])->get('https://api.twitch.tv/helix/streams', [
                'user_id' => $chunk,
            ]);

            if ($response->failed()) {
                throw new Exception("Error al obtener los streams de Twitch.", ErrorCodes::TIMELINE_500);
            }

            $streams = array_merge($streams, $response->json('data'));
        }

        usort($streams, function ($first, $second) {
            return strtotime($second['started_at']) - strtotime($first['started_at']);
        });

        return array_slice($streams, 0, 5);
    }
}
