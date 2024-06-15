<?php

namespace App\Infrastructure\Controllers;

use App\Infrastructure\Requests\GetStreamersRequest;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\StreamerDataManager;

class GetStreamersController extends Controller
{
    private StreamerDataManager $streamerDataManager;

    public function __construct(StreamerDataManager $streamerDataManager)
    {
        $this->streamerDataManager = $streamerDataManager;
    }

    /**
     * Handle the incoming request
     */
    public function __invoke(GetStreamersRequest $request): JsonResponse
    {
        try {
            return response()->json($this->streamerDataManager->getStreamerData($request->input("id")));
        } catch (Exception $exception) {
            $message = match ($exception->getCode()) {
                ErrorCodes::TOKEN_500 => "No se puede establecer conexión con Twitch en este momento",
                ErrorCodes::STREAMERS_500 => "No se pueden devolver streamers en este momento, inténtalo más tarde",
                default => "Internal server error",
            };
            return response()->json(['error' => $message], 503);
        }

    }
}
