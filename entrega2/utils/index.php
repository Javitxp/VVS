<?php
    include "api.php";
    include "database.php";

    function getAndInsertGame($id){
        $conn = connectToDB();
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
        insertNewGame($conn, $allData);
        closeConnectionDB($conn);
        return json_encode($allData);
    }

?>