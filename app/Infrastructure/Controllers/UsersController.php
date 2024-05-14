<?php

namespace App\Infrastructure\Controllers;

use App\Infrastructure\Requests\GetStreamersRequest;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Http\JsonResponse;
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
    public function __invoke(GetStreamersRequest $request): JsonResponse
    {
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
