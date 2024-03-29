<?php

namespace App\Http\Controllers;

use Exception;
use mysqli;

use App\Http\Controllers\ApiController;


class DatabaseController extends Controller
{
    function connectToDB(){
        $servername = env("DB_HOST");
        $username = env("DB_USERNAME");
        $password = env("DB_PASSWORD");
        $database = env("DB_DATABASE");

        // Crear conexión
        $conn = new mysqli($servername, $username, $password, $database);

        // Verificar la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        return $conn;
    }

    function closeConnectionDB($conn){
        $conn->close();
    }

    function insertNewGame($conn, $new){
        try {
            $json_data = json_encode($new);
            $sql = "INSERT INTO Presentar (ID, Datos) VALUES (?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $new["game_id"],$json_data);
            if ($stmt->execute()) {
                error_log("Registro añadido en tops of the tops", 0);
            } else {
                echo "Error al insertar el nuevo registro: " . $stmt->error;
                $this->closeConnectionAndExitDB($conn);
            }
            $stmt->close();
        } catch (Exception $e) {
        }
    }

    function closeConnectionAndExitDB($conn){
        $conn->close();
        exit(-1);
    }

    function setNew40GamesInDB($array, $conn){
        //Primero eliminamos los ultimos 40 registros para meter los del siguiente juego
        $sql = "DELETE FROM Datos";

        // Ejecutar la sentencia SQL
        if ($conn->query($sql) === TRUE) {

            foreach($array as $entry){
                try {
                    $sql = "INSERT INTO Datos (title, user, views, duracion, fecha_creacion) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssiss", $entry["title"], $entry["user_name"], $entry["view_count"], $entry["duration"], $entry["created_at"]);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        error_log("Registro añadido", 0);
                    } else {
                        echo "Error al introducir registros.";
                        $this->closeConnectionAndExitDB($conn);
                    }

                    $stmt->close();
                } catch (Exception $e) {
                }

            }
        } else {
            echo "Error al eliminar registros: " . $conn->error;
            $this->closeConnectionAndExitDB($conn);
        }
    }

    function getVideosFromUserFromDB($user, $conn){
        $sql = "SELECT user, COUNT(*) AS total_videos
        FROM Datos
        WHERE user = '$user'
        GROUP BY user;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["total_videos"];
        }else{
            return -1;
        }
    }

    function getSumViewsFromUserFromDB($user, $conn){
        $sql = "SELECT user, SUM(views) AS total_views
        FROM Datos
        WHERE user = '$user'
        GROUP BY user;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["total_views"];
        }else{
            return -1;
        }
    }

    function getMostViewedFromUserFromDB($user, $conn){
        $sql = "SELECT user, title, max(views) AS views_max, duracion, fecha_creacion
        FROM Datos
        WHERE user = '$user'
        GROUP BY user;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = array(
                "most_viewed_title" => $row["title"],
                "most_viewed_views" => $row["views_max"],
                "most_viewed_duration" => $row["duracion"],
                "most_viewed_created_at" => $row["fecha_creacion"]
            );
            return $data;
        }else{
            return -1;
        }
    }

    function getLast10($id) {
        $conn = $this->connectToDB();
        $sql = "SELECT * FROM Presentar WHERE TIMESTAMPDIFF(MINUTE, Tiempo, NOW()) < 10 AND id = ? LIMIT 1;";
        $statement = $conn->prepare($sql);
        $statement->bind_param("i", $id);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return json_decode($row["Datos"]);
        } else {
            return null;
        }
    }

    function getSince($seconds, $id) {
        $conn = $this->connectToDB();
        $sql = "SELECT * FROM Presentar WHERE TIMESTAMPDIFF(SECOND, Tiempo, NOW()) < ? AND id = ? LIMIT 1;";
        $statement = $conn->prepare($sql);
        $statement->bind_param("ii", $seconds, $id);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return json_decode($row["Datos"]);
        } else {
            return null;
        }
    }

    function checkGameId($id) {
        $conn = $this->connectToDB();
        try {
            $sql = "SELECT COUNT(*) FROM Presentar WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            return $count > 0;
        } catch (Exception $e) {
            echo "Error al verificar el ID del juego: " . $e->getMessage();
            return false;
        }
    }

    function getAndInsertGame($id,$name){
        $apiController = new ApiController();
        $conn = $this->connectToDB();
        $array = $apiController->getTop40Videos($id);
        $topUser = $array[0];
        $user = $topUser["user_name"];
        $this->setNew40GamesInDB($array, $conn);
        $totalVideos = $this->getVideosFromUserFromDB($user, $conn);
        $sum = $this->getSumViewsFromUserFromDB($user, $conn);
        $dataMostViewed = $this->getMostViewedFromUserFromDB($user, $conn);

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
        $this->insertNewGame($conn, $allData);
        $this->closeConnectionDB($conn);
        return $allData;
    }

    function updateGame($id,$name){
        $apiController = new ApiController();
        $conn = $this->connectToDB();
        try {
            $sql = "DELETE FROM Presentar WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {return;}
        $array = $apiController->getTop40Videos($id);
        $topUser = $array[0];
        $user = $topUser["user_name"];
        $this->setNew40GamesInDB($array, $conn);
        $totalVideos = $this->getVideosFromUserFromDB($user, $conn);
        $sum = $this->getSumViewsFromUserFromDB($user, $conn);
        $dataMostViewed = $this->getMostViewedFromUserFromDB($user, $conn);

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
        $this->insertNewGame($conn, $allData);
        $this->closeConnectionDB($conn);
        return $allData;
    }

}
