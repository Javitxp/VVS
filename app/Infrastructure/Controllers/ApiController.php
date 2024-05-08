<?php

namespace App\Infrastructure\Controllers;

use App\Infrastructure\Clients\ApiClient;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function getTop3Games()
    {
        $url = 'https://api.twitch.tv/helix/games/top?first=3';
        $apiClient = new ApiClient();
        $headers = [
            'Authorization' => 'Bearer '.$apiClient->getToken(),
            'Client-Id' => env("CLIENT_ID")
        ];

        $response = Http::withHeaders($headers)->get($url);

        if ($response -> successful()) {
            //$obj = json_decode($response, true);
            $obj = $response->json();
            $top3_games = $obj["data"];
            //return response()->json($top3_games, 200, [], JSON_PRETTY_PRINT);
            return $top3_games;
        } else {
            //return response()->json(['error' => 'Error al realizar la solicitud'], 500);
            return [];
        }
    }

    public function getTop40Videos($id) {
        $url = 'https://api.twitch.tv/helix/videos?game_id='.$id.'&first=40&sort=views';
        $apiClient = new ApiClient();

        $headers = [
            'Authorization' => 'Bearer '.$apiClient->getToken(),
            'Client-Id' => env("CLIENT_ID")
        ];

        $response = Http::withHeaders($headers)->get($url);

        if ($response ->successful()) {
            $obj = json_decode($response, true);
            $top40_videos = $obj["data"];
            return $top40_videos;

        } else {
            //$error = curl_error($curl);
            //echo "Error al realizar la solicitud: " . $error;
            return null;

        }
    }
}
