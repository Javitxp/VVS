<?php

namespace App\Infrastructure\Controllers;

use App\Services\StreamsDataManager;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;

class GetStreamsController extends Controller
{
    private StreamsDataManager $streamsDataManager;

    public function __construct(StreamsDataManager $streamsDataManager)
    {
        $this->streamsDataManager = $streamsDataManager;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(): JsonResponse
    {
        try {
            return response()->json($this->streamsDataManager->getStreams());
        } catch (Exception $exception) {
            $message = match ($exception->getCode()) {
                ErrorCodes::TOKEN_500 => "No se puede establecer conexión con Twitch en este momento",
                ErrorCodes::STREAMS_500 => "No se pueden devolver streams en este momento, inténtalo más tarde",
                default => "Internal server error",
            };
            return response()->json(['error' => $message], 503);
        }
    }
}
