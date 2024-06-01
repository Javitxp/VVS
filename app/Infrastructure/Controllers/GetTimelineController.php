<?php

namespace App\Infrastructure\Controllers;

use App\Services\UserDataManager;
use Exception;
use Illuminate\Http\JsonResponse;

class GetTimelineController extends Controller
{
    private UserDataManager $userDataManager;

    public function __construct(UserDataManager $userDataManager)
    {
        $this->userDataManager = $userDataManager;
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke($userId): JsonResponse
    {
        try {
            $timeline = $this->userDataManager->getUserFollowedStreamersTimeline($userId);
            return response()->json($timeline);
        } catch (Exception $e) {
            if ($e->getCode() == 404) {
                return response()->json(['message' => $e->getMessage()], 404);
            } else {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }
    }
}
