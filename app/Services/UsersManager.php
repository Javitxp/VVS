<?php

namespace App\Services;

use App\Models\UserRegistred;
use Exception;

class UserDataManager
{
    private TokenProvider $tokenProvider;
    private UserDataProvider $usersDataProvider;

    public function __construct(TokenProvider $tokenProvider, UserDataProvider $usersDataProvider)
    {
        $this->tokenProvider = $tokenProvider;
        $this->usersDataProvider = $usersDataProvider;
    }

    /**
     * @throws Exception
     */
    public function getUserData($userId)
    {
        return $this->streamerDataProvider->getUserData($userId);
    }

    /**
     * @throws Exception
     */
    public function updateUserFollowedStreamers($userId, $followedStreamers)
    {
        try {
            // Buscar al usuario por su ID en la base de datos
            $user = UserRegistred::find($userId);

            if (!$user) {
                throw new Exception("User not found", ErrorCodes::USER_404);
            }

            // Actualizar los followedStreamers en el modelo y guardar en la base de datos
            $user->followedStreamers = $followedStreamers;
            $user->save();

            // Puedes devolver los datos actualizados si es necesario
            return $user->toArray();
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::USER_500);
        }
    }

    /**
     * @throws Exception
     */
    public function createUser($userId, $username, $password)
    {
        try {
            // Buscar al usuario por su ID en la base de datos
            $user = UserRegistred::find($userId);

            if (!$user) {
                throw new Exception("User not found", ErrorCodes::USER_404);
            }

            // Actualizar los followedStreamers en el modelo y guardar en la base de datos
            $user->followedStreamers = "{}";
            $user->save();

            // Puedes devolver los datos actualizados si es necesario
            return $user->toArray();
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::USER_500);
        }
    }

    /**
     * @throws Exception
     */
    public function deleteUser($userId)
    {
        try {
            $user = UserRegistred::find($userId);

            if (!$user) {
                throw new Exception("User not found", ErrorCodes::USER_404);
            }

            $user->delete();
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::USER_500);
        }
    }
}
