<?php

namespace App\Http\Controllers;

use App\Services\ApiTwitch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopsOfTheTopsController extends Controller
{
    protected $apiTwitch;

    public function __construct(ApiTwitch $apiTwitch)
    {
        $this->apiTwitch = $apiTwitch;
    }

    /**
     * Handle the incoming request.
     */
    public function getTopOfTheTops(Request $request): JsonResponse
    {
        return $this->apiTwitch->getTopOfTheTops($request);
    }
}
