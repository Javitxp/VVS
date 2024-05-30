<?php

namespace App\Services;

use App\Infrastructure\Clients\ApiClient;
use App\Models\UserRegistred;
use App\Utilities\ErrorCodes;
use Exception;

class StreamerDataProvider
{
    private ApiClient $apiClient;
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws Exception
     */
    public function getUserData($userId)
    {
        try {
            $user = UserRegistred::find($userId);

            if (!$user) {
                //Cambiar código de error
                throw new Exception("User not found", ErrorCodes::USER_404);
            }

            return $user->toArray();
        } catch (Exception $e) {
            throw new Exception("Error: Code 500", ErrorCodes::USER_500);
        }
    }
}
