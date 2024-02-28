<?php

    include '../../utils/index.php';

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(405);
        echo "Only GET requests";
        exit(-1);
    }

    $json = isset($_GET["since"]) ? getSinceTopOfTheTops($_GET["since"]) : getLast10MinTopOfTheTops();

    $top3_games = getTop3Games();

    if($json === null || !areIDSEqual($json)){
        $json = getAndInsertTopOfTheTops();
    }

    header("Content-Type: application/json");
    echo $json;
        
?>
