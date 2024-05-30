<?php

namespace App\Infrastructure\Controllers;

use App\Infrastructure\Requests\CreateUserRequest;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\UserDataManager;

class CreateUserController extends Controller
{
    private UserDataManager $userDataManager;

    public function __construct(UserDataManager $userDataManager)
    {
        $this->userDataManager = $userDataManager;
    }

    /**
     * Handle the incoming request
     */
    public function __invoke(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userDataManager->createUser(
                $request->input("username"),
                $request->input("password")
            );
            return response()->json([
                'username' => $user->username,
                'message' => 'Usuario creado correctamente'
            ], 201);
        } catch (Exception $exception) {
            $message = match ($exception->getCode()) {
                ErrorCodes::USERS_400 => "Los parámetros requeridos (username y password) no fueron proporcionados.",
                ErrorCodes::USERS_409 => "El nombre de usuario ya está en uso.",
                ErrorCodes::USERS_500 => "Error del servidor al crear el usuario.",
                default => "Internal server error",
            };
            return response()->json(['error' => $message], 503);
        }
    }
}
