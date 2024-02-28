<?php

    include '../../utils/index.php';

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(405);
        echo "Only GET requests";
        exit(-1);
    }

        $top3_games = getTop3Games();
        if ($top3_games === null) {
            echo "Error al obtener top 3 juegos";
            exit(-1);
        }

        $topsOfTheTops = [];
        foreach ($top3_games as $game) {
            $id = $game["id"];
            $name = $game["name"];

            $result = checkGameId($id);
            if($result){
                $json = isset($_GET["since"]) ? getSince($_GET["since"],$id) : getLast10($id);
                if($json === null){
                    $json = updateGame($id,$name);
                }
                $topsOfTheTops[] = $json;
            }
            else{
                $json = getAndInsertGame($id,$name);
                $topsOfTheTops[] = $json;
            }
        }

    header("Content-Type: application/json");
    echo json_encode($topsOfTheTops);
        
?>
