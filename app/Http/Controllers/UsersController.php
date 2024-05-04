<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ApiTwitch;

class UsersController extends Controller
{
    protected $apiTwitch;

    public function __construct(ApiTwitch $apiTwitch)
    {
        $this->apiTwitch = $apiTwitch;
    }

    /**
     * Handle the incoming request
     */
    public function __invoke(Request $request): JsonResponse
    {
        return $this->apiTwitch->getUsers($request);
    }
}
