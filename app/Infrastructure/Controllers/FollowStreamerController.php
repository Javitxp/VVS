<?php

namespace App\Infrastructure\Controllers;

use App\Infrastructure\Requests\FollowStreamerRequest;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\UserDataManager;
use App\Services\StreamerDataManager;

class FollowStreamerController extends Controller
{
    private UserDataManager $userDataManager;
    private StreamerDataManager $streamerDataManager;

    public function __construct(UserDataManager $userDataManager, StreamerDataManager $streamerDataManager)
    {
        $this->userDataManager = $userDataManager;
        $this->streamerDataManager = $streamerDataManager;
    }

    /**
     * Handle the incoming request
     */
    public function __invoke(FollowStreamerRequest $request): JsonResponse
    {

        try {
            $userId = $request->input("userId");
            $streamerId = $request->input("streamerId");

            $this->streamerDataManager->getStreamerData($request->input("streamerId"));

            $user = $this->userDataManager->followStreamer(
                $userId,
                $streamerId
            );
            return response()->json([
                'message' => 'Ahora sigues a '. $streamerId
            ], 201);

        } catch (Exception $exception) {
            $message = match ($exception->getCode()) {
                ErrorCodes::USERS_400 => "Los parámetros requeridos (username y password) no fueron proporcionados.",
                ErrorCodes::USERS_404 => "El usuario ( ".$userId." ) especificado no existe en la API.",
                ErrorCodes::STREAMERS_404 => "El streamer ( ".$streamerId." ) especificado no existe en la API.",
                ErrorCodes::FOLLOW_409 => "409 Conflict : El usuario ya está siguiendo al streamer.",
                ErrorCodes::USERS_409 => "El nombre de usuario ya está en uso.",
                ErrorCodes::USERS_500 => "Error del servidor al crear el usuario.",
                ErrorCodes::TOKEN_500 => "No se puede establecer conexión con Twitch en este momento",
                ErrorCodes::STREAMERS_500 => "No se pueden devolver streamers en este momento, inténtalo más tarde",
                default => "Internal Server Error : Error del servidor al seguir al streamer.",
            };
            $status = match ($exception->getCode()) {
                ErrorCodes::USERS_400 => 400,
                ErrorCodes::FOLLOW_409 => 409,
                ErrorCodes::USERS_409 => 409,
                ErrorCodes::USERS_404 => 404,
                ErrorCodes::STREAMERS_404 => 404,
                ErrorCodes::USERS_500 => 500,
                ErrorCodes::STREAMERS_500 => 500,
                ErrorCodes::TOKEN_500 => 500,
                default => 503,
            };
            return response()->json(['error' => $message], $status);
        }
    }
}
