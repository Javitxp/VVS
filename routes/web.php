<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\DatabaseController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/analytics/streams', function () {
    // Verificar si se recibió una solicitud GET
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(405);
        echo "Only GET requests";
        exit(-1);
    }

    //Configurar url y autorizacion y client-id en header
    $url = 'https://api.twitch.tv/helix/streams';

    $headers = array(
        'Authorization: Bearer m8n110x82us492oc94ciqwx97iuo3t',
        'Client-Id: rxbua83lt6p4yqdig92dvsoicmdi87'
    );

    //Configurar curl
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPGET, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);

    if ($response === false) {
        $error = curl_error($curl);
        echo "Error al realizar la solicitud: " . $error;
    } else {
        // Decodificar la respuesta JSON
        $streams_data = json_decode($response, true);

        // Verificar si hay datos disponibles
        if (isset($streams_data['data'])) {
            // Obtener la información necesaria (nombre del usuario y título del stream)
            $stream_info = array();
            foreach ($streams_data['data'] as $stream) {
                $stream_info[] = array(
                    'title' => $stream['title'],
                    'user_name' => $stream['user_name']
                );
            }

            // Imprimir la información en formato JSON
            header("Content-Type: application/json");
            $data = json_encode($stream_info);

        } else {
            $data = "No se encontraron datos de streams.";
        }
    }

    curl_close($curl);
    return response($data,200)
            ->header("Content-Type","application/json");
});

Route::get('analytics/users', function (){
    // Verificar si se recibió una solicitud GET
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(405);
        echo "Only GET requests";
        exit(-1);
    }

    // Verificar si se proporcionó el parámetro id
    $id = $_GET['id'] ?? null; // Usar el operador de fusión de null para asignar null si 'id' no está presente

    if (!isset($id)) {
        echo "Parameter id required";
        exit(-1);
    }

    // Configurar la URL y la autorización en el encabezado
    $url = 'https://api.twitch.tv/helix/users?id='.$id;

    $headers = array(
        'Authorization: Bearer m8n110x82us492oc94ciqwx97iuo3t',
        'Client-Id: rxbua83lt6p4yqdig92dvsoicmdi87'
    );

    // Configurar curl
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPGET, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);

    if ($response === false) {
        $error = curl_error($curl);
        echo "Error al realizar la solicitud: " . $error;
    } else {
        $obj = json_decode($response, true);
        $array = $obj["data"];
        if($array == null || count($array) == 0){
            echo "There is no user with id: ".$id;
            exit(-1);
        }
        $json = json_encode($array[0]);
        return response($json,200)
                ->header("Content-Type","application/json");
    }

    curl_close($curl);
});


Route::get('analytics/topsofthetops', function (){
    $apiController = new ApiController();
    $dbController = new DatabaseController();
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(405);
        echo "Only GET requests";
        exit(-1);
    }

    $top3_games = $apiController->getTop3Games();
    if ($top3_games === null) {
        echo "Error al obtener top 3 juegos";
        exit(-1);
    }

    $since = $_GET['since'] ?? null;

    $topsOfTheTops = [];
    foreach ($top3_games as $game) {
        $id = $game["id"];
        $name = $game["name"];

        $result = $dbController->checkGameId($id);
        if($result){
            $json = isset($since) ? $dbController->getSince($since,$id) : $dbController->getLast10($id);
            if($json === null){
                $json = $dbController->updateGame($id,$name);
            }
            $topsOfTheTops[] = $json;
        }
        else{
            $json = $dbController->getAndInsertGame($id,$name);
            $topsOfTheTops[] = $json;
        }
    }

    $data = json_encode($topsOfTheTops);
    return response($data,200)
            ->header("Content-Type","application/json");

});

