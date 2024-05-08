<?php

namespace App\Infrastructure\Controllers;

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
        $this->userDataManager->setUserId($request->input("id"));
        return response()->json($this->userDataManager->getUserData());

    }
}
