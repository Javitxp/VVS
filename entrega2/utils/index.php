<?php
    include "api.php";
    include "database.php";

    function getAndInsertTopOfTheTops(){
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
            $array = getTop40Videos($id);

            $topUser = $array[0];
            $user = $topUser["user_name"];
            setNew40GamesInDB($array, $conn);

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
        }

        insertNewTopOfTheTops($conn, json_encode($topsOfTheTops));
        closeConnectionDB($conn);
        return json_encode($topsOfTheTops);
    }

    function areIDSEqual($json){
        $top3_games = getTop3Games();
        $bool = false;
        $jsonArray = json_decode($json, true);
        if($top3_games[0]["id"] == $jsonArray[0]["game_id"] && $top3_games[1]["id"] == $jsonArray[1]["game_id"] && $top3_games[2]["id"] == $jsonArray[2]["game_id"]){
            $bool = true;
        }
        return $bool;
    }

    function selectGamesInOurTop($json){
        $top3_games = getTop3Games();
        $newJson = array();
        $jsonArray = json_decode($json, true);
        for($i = 0; $i < 3; $i++){
            if($jsonArray[$i]["id"] == $top3_games[$i]["game_id"]){
                $newJson[] = $jsonArray[$i];
            }
        }
        return $newJson;
    }

?>