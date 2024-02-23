<?php

    include '../../utils/index.php';

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(405);
        echo "Only GET requests";
        exit(-1);
    }

    $json = getAndInsertTopOfTheTops();
    header("Content-Type: application/json");
    echo $json;
        
?>
