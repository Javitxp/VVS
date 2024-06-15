<?php

use App\Infrastructure\Clients\ApiClient;
use App\Services\StreamsDataProvider;
use Tests\TestCase;

class StreamsDataProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClientMock = Mockery::mock(ApiClient::class);
        $this->strDataProvider = new StreamsDataProvider($this->apiClientMock);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetStreams()
    {
        $token = "token";
        $headers = array('Authorization: Bearer ' . $token);
        $url = 'https://api.twitch.tv/helix/streams';
        $this->apiClientMock->expects('makeCurlCall')
            ->with($url, $headers)
            ->andReturn(json_encode(["data" => [
                'title' => 'title',
                'user_name' => 'user_name'
            ]]));

        $response = $this->strDataProvider->execute($token);

        $this->assertEquals([
            'title' => 'title',
            'user_name' => 'user_name'
        ], $response);
    }
}
