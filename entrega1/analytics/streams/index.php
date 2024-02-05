<?php

    $url = 'https://api.twitch.tv/helix/streams';

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
            echo $data;

        } else {
            echo "No se encontraron datos de streams.";
        }  
    }

    curl_close($curl);
?>
