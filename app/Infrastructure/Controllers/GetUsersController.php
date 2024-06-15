<?php

namespace App\Infrastructure\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\UserDataManager;

class GetUsersController extends Controller
{
    private UserDataManager $userDataManager;

    public function __construct(UserDataManager $userDataManager)
    {
        $this->userDataManager = $userDataManager;
    }

    /**
     * Handle the incoming request
     */
    public function __invoke(): JsonResponse
    {
        try {
            $users = $this->userDataManager->getAllUsers();
            return response()->json($users, 200);
        } catch (Exception $exception) {
            return response()->json(['error' => 'Error del servidor al obtener la lista de usuarios.'], 500);
        }
    }
}
