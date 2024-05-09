<?php

namespace App\Infrastructure\Controllers;

use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UserDataManager;

class UsersController extends Controller
{
    private $userDataManager;

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
        $this->userDataManager->setUserId($request->input("id"));
        try {
            return response()->json($this->userDataManager->getUserData());
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case ErrorCodes::TOKEN_500:
                    return response()->json(['error' => 'No se puede establecer conexión con Twitch en este momento'], 500);
                case ErrorCodes::USERS_500:
                    return response()->json(['error' => 'No se pueden devolver usuarios en este momento, inténtalo más tarde'], 500);
                default:
                    return response()->json(['error' => 'Internal server error'], 500);
            }
        }

    }
}
