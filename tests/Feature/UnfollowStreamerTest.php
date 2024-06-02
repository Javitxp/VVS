<?php

use App\Models\RegistredUser;
use App\Services\UserDataProvider;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;

class UnfollowStreamerTest extends TestCase
{
    /**
     * @test
     */
    public function UnfollowSuccessful()
    {
        $userId = "1";
        $streamerId = "1";
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $userDataProviderMock->expects('unfollowStreamer')
            ->with($userId, $streamerId)
            ->andReturn(new RegistredUser());
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $response = $this->delete('analytics/unfollow', ['userId' => $userId, 'streamerId' => $streamerId]);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Dejaste de seguir a ' . $streamerId
        ]);
    }
    /**
     * @test
     */
    public function WhenUserIsNotOnTheApiReturns404()
    {
        $userId = "1";
        $streamerId = "1";
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $userDataProviderMock->expects('unfollowStreamer')
            ->with($userId, $streamerId)
            ->andThrow(new Exception("Error", ErrorCodes::USERS_404));
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);
        $response = $this->delete('analytics/unfollow', ['userId' => $userId, 'streamerId' => $streamerId]);
        $response->assertStatus(404);
        $response->assertJson([
            "error" => "El usuario ( " . $userId . " ) o el streamer ( " . $streamerId . " )",
        ]);
    }
    /**
     * @test
     */
    public function WhenServerFailsReturns500()
    {
        $userId = "1";
        $streamerId = "1";
        $userDataProviderMock = Mockery::mock(UserDataProvider::class);
        $userDataProviderMock->expects('unfollowStreamer')
            ->with($userId, $streamerId)
            ->andThrow(new Exception("Server Error", 500));
        $this->app->instance(UserDataProvider::class, $userDataProviderMock);

        $response = $this->delete('analytics/unfollow', ['userId' => $userId, 'streamerId' => $streamerId]);

        $response->assertStatus(500);
        $response->assertJson([
            "error" => "Internal Server Error : Error del servidor al dejar de seguir al streamer.",
        ]);
    }
}
