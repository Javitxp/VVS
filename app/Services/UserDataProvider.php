<?php

namespace App\Services;

use App\Models\RegistredUser;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Support\Facades\Hash;

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
}

