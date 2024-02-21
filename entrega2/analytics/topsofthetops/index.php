<?php

    include "../../utils/getTop3Games.php";
    include "../../utils/getTop40Videos.php";
    include "../../utils/database.php";

    // Verificar si se recibió una solicitud GET
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(405);
        echo "Only GET requests";
        exit(-1);
    }

    $conn = connectToDB();

    $top3_games = getTop3Games();
    if($top3_games === null){
        echo "Error al obtener top 3 juegos";
        exit(-1);
    }

    $topsOfTheTops = array();
    foreach ($top3_games as $game) {
        $id = $game["id"];
        $name = $game["name"];
        $array = getTop40Videos($id);

        $topUser = $array[0];
        $user = $topUser["user_name"];
        setNew40GamesInDB($array,$conn);

        $totalVideos = getVideosFromUserFromDB($user, $conn);
        $sum = getSumViewsFromUserFromDB($user, $conn);
        $dataMostViewed = getMostViewedFromUserFromDB($user, $conn);

        $allData = array(
            "game_id" => $id,
            "game_name" => $name,
            "user_name" => $user,
            "total_videos" => $totalVideos,
            "total_views" => $sum,
            "most_viewed_title" => $dataMostViewed["most_viewed_title"],
            "most_viewed_views" => $dataMostViewed["most_viewed_views"],
            "most_viewed_duration" => $dataMostViewed["most_viewed_duration"],
            "most_viewed_created_at" => $dataMostViewed["most_viewed_created_at"]
        );

        $topsOfTheTops[] = $allData;

        insertNewTopOfTheTops($conn, $allData);
    }
    $json = json_encode($topsOfTheTops);
    header("Content-Type: application/json");
    echo $json;

    closeConnectionDB($conn);
        
?>
