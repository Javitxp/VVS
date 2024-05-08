<?php

namespace App\Infrastructure\Controllers;

use App\Infrastructure\Clients\ApiClient;

class ApiController extends Controller
{
    public function getTop3Games() {
        $url = 'https://api.twitch.tv/helix/games/top?first=3';
        $apiClient = new ApiClient();

        $headers = [
            'Authorization' => 'Bearer '.$apiClient->getToken(),
            'Client-Id' => env("CLIENT_ID")
        ];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response === false) {
            //$error = curl_error($curl);
            //echo "Error al realizar la solicitud: " . $error;
            return null;
        } else {
            $obj = json_decode($response, true);
            echo $obj;
            $top3_games = $obj["data"];
            return $top3_games;
        }
    }

    public function getTop40Videos($id) {
        $url = 'https://api.twitch.tv/helix/videos?game_id='.$id.'&first=40&sort=views';
        $apiClient = new ApiClient();

        $headers = [
            'Authorization' => 'Bearer '.$apiClient->getToken(),
            'Client-Id' => env("CLIENT_ID")
        ];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response === false) {
            //$error = curl_error($curl);
            //echo "Error al realizar la solicitud: " . $error;
            return null;
        } else {
            $obj = json_decode($response, true);
            $top3_games = $obj["data"];
            return $top3_games;
        }
    }
}
