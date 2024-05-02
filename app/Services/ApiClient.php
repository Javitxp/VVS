<?php

namespace App\Services;

class ApiClient
{
    private const CLIENT_ID = 'rxbua83lt6p4yqdig92dvsoicmdi87';
    private const CLIENT_SECRET = '';

    public function getToken($url): String{
        $data = array(
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'grant_type' => 'client_credentials'
        );

        $curlHeaders = curl_init();
        curl_setopt($curlHeaders, CURLOPT_URL, $url);
        curl_setopt($curlHeaders, CURLOPT_POST,1);
        curl_setopt($curlHeaders, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curlHeaders, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHeaders, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));

        $response = curl_exec($curlHeaders);

        if(curl_errno($curlHeaders)){
            echo 'Error en la peticion para obtener token';
            exit;
        }

        curl_close($curlHeaders);
        return $response;
    }

    public function makeCurlCall($url, $headers): String{
        $curlHeaders = curl_init();

        $headers[] = 'Client-Id: rxbua83lt6p4yqdig92dvsoicmdi87';

        curl_setopt($curlHeaders, CURLOPT_URL, $url);
        curl_setopt($curlHeaders, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHeaders, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));

        $response = curl_exec($curlHeaders);
        $http_status = curl_getinfo($curlHeaders, CURLINFO_HTTP_CODE);

        if(curl_errno($curlHeaders)){
            echo 'Error en la peticion para obtener token';
            exit;
        }

        curl_close($curlHeaders);
        return $response;
    }
}
