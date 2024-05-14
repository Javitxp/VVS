<?php

namespace App\Infrastructure\Controllers;

use App\Services\StreamsDataManager;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;

class StreamsController extends Controller
{
    private $streamsDataManager;

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
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case ErrorCodes::TOKEN_500:
                    $msg = "No se puede establecer conexión con Twitch en este momento";
                    break;
                case ErrorCodes::STREAMS_500:
                    $msg = "No se pueden devolver streams en este momento, inténtalo más tarde";
                    break;
                default:
                    $msg = "Internal server error";
                    break;
            }
            return response()->json(['error' => $msg], 503);
        }
    }
}
