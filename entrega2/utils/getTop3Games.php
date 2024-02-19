<?php 

function getTop3Games() {
    //Configurar url y autorizacion y client-id en header
    $url = 'https://api.twitch.tv/helix/games/top?first=3';

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
        //$error = curl_error($curl);
        //echo "Error al realizar la solicitud: " . $error;
        return null;
    } else {
        $obj = json_decode($response, true);
        $top3_games = $obj["data"];
        return $top3_games;        
    }
    curl_close($curl);
}

?>