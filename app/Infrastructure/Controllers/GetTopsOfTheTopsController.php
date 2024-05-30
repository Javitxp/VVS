<?php

namespace App\Infrastructure\Controllers;

use App\Services\TopsOfTheTopsDataManager;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetTopsOfTheTopsController extends Controller
{
    protected TopsOfTheTopsDataManager $topsOfTheTopsDataManager;
    public function __construct(TopsOfTheTopsDataManager $topsOfTheTopsDataManager)
    {
        $this->topsOfTheTopsDataManager = $topsOfTheTopsDataManager;
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            return response()->json($this->topsOfTheTopsDataManager->getTopsOfTheTops($request->input("since", "")));
        } catch (Exception $exception) {
            $message = match ($exception->getCode()) {
                ErrorCodes::TOKEN_500 => "No se puede establecer conexión con Twitch en este momento",
                ErrorCodes::TOP3GAMES_500 => "No se pueden devolver los 3 mejores juegos en este momento, inténtalo más tarde",
                ErrorCodes::TOP40VIDEOS_500 => "No se pueden devolver los 40 mejores videos en este momento, inténtalo más tarde",
                default => "Internal server error",
            };
            return response()->json(['error' => $message], 503);
        }
    }
}
