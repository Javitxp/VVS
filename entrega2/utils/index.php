<?php
    include "api.php";
    include "database.php";

    function getAndInsertGame($id,$name){
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
        return $allData;
    }

    function updateGame($id,$name){
        $conn = connectToDB();
        try {
            $sql = "DELETE FROM Presentar WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo "La fila se eliminó correctamente.";
            } else {
                echo "Error al eliminar la fila: " . $stmt->error;
            }
            $stmt->close();
        } catch (Exception $e) {
            echo "Error al eliminar la fila: " . $e->getMessage();
        }

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
        return $allData;
    }

?>