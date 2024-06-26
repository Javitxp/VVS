<?php

namespace App\Infrastructure\Controllers;

use App\Infrastructure\Requests\FollowStreamerRequest;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\UserDataManager;
use App\Services\StreamerDataManager;

class UnfollowStreamerController extends Controller
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

            $user = $this->userDataManager->unfollowStreamer(
                $userId,
                $streamerId
            );
            return response()->json([
                'message' => 'Dejaste de seguir a ' . $streamerId
            ], 200);

        } catch (Exception $exception) {
            $message = match ($exception->getCode()) {
                ErrorCodes::USERS_404, ErrorCodes::STREAMERS_404 => "El usuario ( " . $userId . " ) o el streamer ( " . $streamerId . " ) especificado no existe en la API.",
                default => "Internal Server Error : Error del servidor al dejar de seguir al streamer.",
            };
            $status = match ($exception->getCode()) {
                ErrorCodes::USERS_404, ErrorCodes::STREAMERS_404 => 404,
                default => 500,
            };
            return response()->json(['error' => $message], $status);
        }
    }
}
