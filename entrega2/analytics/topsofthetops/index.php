<?php

    include "../../utils/getTop3Games.php";
    include "../../utils/getTop40Videos.php";

    // Verificar si se recibió una solicitud GET
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(405);
        echo "Only GET requests";
        exit(-1);
    }

    $top3_games = getTop3Games();
    if($top3_games === null){
        echo "Error al obtener top 3 juegos";
        exit(-1);
    }

    foreach ($top3_games as $game) {
        $id = $game["id"];
        //echo $id;
        //$array = getTop40Videos($id);
        //$json = json_encode($array);
//header("Content-Type: application/json");
        //echo $json;
    }
    $json = json_encode($top3_games);
        header("Content-Type: application/json");
        echo $json;
        
?>
