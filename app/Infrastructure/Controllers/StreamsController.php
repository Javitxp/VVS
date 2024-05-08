<?php

namespace App\Infrastructure\Controllers;

use App\Services\ApiTwitch;
use App\Services\StreamsDataManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StreamsController extends Controller
{
    protected $apiTwitch;

    private $streamsDataManager;

    public function __construct(StreamsDataManager $streamsDataManager)
    {
        $this->streamsDataManager = $streamsDataManager;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        return $this->apiTwitch->getStreams($request);
    }
}
