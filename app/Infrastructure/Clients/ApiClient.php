<?php

namespace App\Infrastructure\Clients;

use Exception;
use App\Utilities\ErrorCodes;

class ApiClient
{
    private const CLIENT_SECRET = 'i133qcoopm5wy8enea8df8tugvo07j';

    public function getToken(): String
    {
        $url = 'https://id.twitch.tv/oauth2/token';
        $data = array(
            'client_id' => env("CLIENT_ID"),
            'client_secret' => self::CLIENT_SECRET,
            'grant_type' => 'client_credentials'
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));

        $response = curl_exec($curl);

        if(curl_errno($curl)) {
            echo 'Error en la peticion para obtener token';
            exit;
        }

        curl_close($curl);

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_code == 500) {
            throw new Exception("Error: Code 500", ErrorCodes::TOKEN_500);
        }

        $jsonResponse = json_decode($response, true);

        if (isset($jsonResponse['access_token'])) {
            return $jsonResponse['access_token'];
        } else {
            echo "No se pudo encontrar el access_token en la respuesta.";
            exit;
        }
    }

    /**
     * @throws Exception
     */
    public function makeCurlCall($url, $headers): String
    {
        $curlHeaders = curl_init();

        $headers[] = 'Client-Id: ' . env("CLIENT_ID");

        curl_setopt($curlHeaders, CURLOPT_URL, $url);
        curl_setopt($curlHeaders, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHeaders, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curlHeaders);

        if(curl_errno($curlHeaders)) {
            echo 'Error en la peticion para obtener token';
            exit;
        }

        curl_close($curlHeaders);

        $http_code = curl_getinfo($curlHeaders, CURLINFO_HTTP_CODE);
        if ($http_code == 500) {
            throw new Exception("Error: Code 500");
        }

        return $response;
    }

    /**
     * @throws Exception
     */
    public function getTop3Games($headers)
    {
        $url = 'https://api.twitch.tv/helix/games/top?first=3';

        $headers[] = 'Client-Id: ' . env("CLIENT_ID");

        return $this->makeCurlCall($url, $headers);
    }

    /**
     * @throws Exception
     */
    public function getTop40Videos($id, $headers)
    {
        $url = 'https://api.twitch.tv/helix/videos?game_id='.$id.'&first=40&sort=views';

        $headers[] = 'Client-Id: ' . env("CLIENT_ID");

        return $this->makeCurlCall($url, $headers);
    }
}
