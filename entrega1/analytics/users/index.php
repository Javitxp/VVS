<?php

// Verificar si se recibió una solicitud GET
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo "Only GET requests";
    exit(-1);
}

// Verificar si hay parámetro id
if (!isset($_GET['id'])) {
    echo "Parameter id required";
    exit(-1);
}

//Configurar url y autorizacion y client-id en header
$url = 'https://api.twitch.tv/helix/users?id='.$_GET["id"];

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
    header("Content-Type: application/json");
    $obj = json_decode($response, true);
    $array = $obj["data"];
    $json = json_encode($array[0]);
    echo $json;

}

curl_close($curl);

?>