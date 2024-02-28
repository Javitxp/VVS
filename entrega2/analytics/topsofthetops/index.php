<?php

    include '../../utils/index.php';

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(405);
        echo "Only GET requests";
        exit(-1);
    }

    /*$json = isset($_GET["since"]) ? getSinceTopOfTheTops($_GET["since"]) : getLast10MinTopOfTheTops();

    if($json === null){
        $json = getAndInsertTopOfTheTops();
    }
    */

    $conn = connectToDB();
        $top3_games = getTop3Games();
        if ($top3_games === null) {
            echo "Error al obtener top 3 juegos";
            exit(-1);
        }
        $topsOfTheTops = [];
        foreach ($top3_games as $game) {
            $id = $game["id"];
            $name = $game["name"];

            // CHECK SI ESTA EN PRESENTAR CON ID
            $result = checkGameId($id);

            // ESTA EN PRESENTAR
            if($result){
                // REVISAR TIMESTAMP
                // SI HAY SINCE
                    // COMPARAR SINCE CON TIMESTAMP
                // NO HAY SINCE
                    // COMPARAR 10MIN CON TIMESTAMP  
                $json = isset($_GET["since"]) ? getSince($_GET["since"],$id) : getLast10($id);
                if($json === null){
                    //$json = getAndInsertGame($id);
                    $json = updateGame($id);
                }
            }
            // NO ESTA EN PRESENTAR
            else{
                // OBTENER TODA LA DATA DEL JUEGO
                $json = getAndInsertGame($id);
            }
        }

    header("Content-Type: application/json");
    echo $json;
        
?>
