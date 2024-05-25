<?php

namespace App\Infrastructure\Controllers;

use App\Services\TopsOfTheTopsDataManager;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetTopsOfTheTopsController extends Controller
{
    protected TopsOfTheTopsDataManager $topsDataManager;
    public function __construct(TopsOfTheTopsDataManager $topsDataManager)
    {
        $this->topsDataManager = $topsDataManager;
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            return response()->json($this->topsDataManager->getTopsOfTheTops($request->input("since")));
        } catch (Exception $e) {
            $msg = match ($e->getCode()) {
                ErrorCodes::TOKEN_500 => "No se puede establecer conexión con Twitch en este momento",
                ErrorCodes::TOP3GAMES_500 => "No se pueden devolver los 3 mejores juegos en este momento, inténtalo más tarde",
                ErrorCodes::TOP40VIDEOS_500 => "No se pueden devolver los 40 mejores videos en este momento, inténtalo más tarde",
                default => "Internal server error",
            };
            return response()->json(['error' => $msg], 503);
        }
    }
}
