<?php

namespace App\Infrastructure\Controllers;

class ApiController extends Controller
{
    public function getTop3Games() {
        $url = 'https://api.twitch.tv/helix/games/top?first=3';

        $headers = array(
            'Authorization: Bearer m8n110x82us492oc94ciqwx97iuo3t',
            'Client-Id: rxbua83lt6p4yqdig92dvsoicmdi87'
        );

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

    public function getTop40Videos($id) {
        $url = 'https://api.twitch.tv/helix/videos?game_id='.$id.'&first=40&sort=views';

        $headers = array(
            'Authorization: Bearer m8n110x82us492oc94ciqwx97iuo3t',
            'Client-Id: rxbua83lt6p4yqdig92dvsoicmdi87'
        );

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
