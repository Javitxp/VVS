<?php

namespace App\Infrastructure\Controllers;

use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UserDataManager;

class UsersController extends Controller
{
    private UserDataManager $userDataManager;

    public function __construct(UserDataManager $userDataManager)
    {
        $this->userDataManager = $userDataManager;
    }

    /**
     * Handle the incoming request
     */
    public function __invoke(Request $request): JsonResponse
    {
        if(!$request->has("id")) {
            return response()->json(['message' => 'Parameter id required'], 500);
        }
        if (!is_numeric($request->input("id"))) {
            return response()->json(['message' => 'Parameter id must be a number'], 500);
        }
        try {
            return response()->json($this->userDataManager->getUserData($request->input("id")));
        } catch (Exception $e) {
            $msg = match ($e->getCode()) {
                ErrorCodes::TOKEN_500 => "No se puede establecer conexión con Twitch en este momento",
                ErrorCodes::USERS_500 => "No se pueden devolver usuarios en este momento, inténtalo más tarde",
                default => "Internal server error",
            };
            return response()->json(['error' => $msg], 503);
        }

    }
}
