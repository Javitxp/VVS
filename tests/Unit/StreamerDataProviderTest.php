<?php

use App\Infrastructure\Clients\ApiClient;
use App\Services\StreamerDataProvider;
use Tests\TestCase;

class StreamerDataProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->strDataProvider = new StreamerDataProvider($this->apiClientMock);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetStreamer()
    {
        $token = "token";
        $streamerId = "1";
        $headers = array('Authorization: Bearer ' . $token);
        $url = 'https://api.twitch.tv/helix/users?id='.$streamerId;
        $this->apiClientMock->expects('makeCurlCall')
            ->with($url, $headers)
            ->andReturn(json_encode(["data" => ["streamer_id" => $streamerId, "streamer_name" => "streamer1"]]));

        $response = $this->strDataProvider->getStreamerData($token, $streamerId);

        $this->assertEquals(["streamer_id" => $streamerId, "streamer_name" => "streamer1"], $response);
    }
}
