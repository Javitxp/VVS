<?php
    function connectToDB(){
        $servername = "localhost";
        $username = "id21862115_los_parseros";
        $password = "P4rs3r0s*";
        $database = "id21862115_twitch";

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

    function insertNewTopOfTheTops($conn, $new){
        try {
            $json_data = json_encode($new);
            $sql = "INSERT INTO Twitch_Entrega2 (Datos) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $json_data);
            if ($stmt->execute()) {
                error_log("Registro añadido en tops of the tops", 0);
            } else {
                echo "Error al insertar el nuevo registro: " . $stmt->error;
                closeConnectionAndExitDB($conn);
            }
            $stmt->close();
        } catch (Exception $e) {
            echo "Excepción capturada: " . $e->getMessage();
            echo "<br>";
        }
    }

    function closeConnectionAndExitDB($conn){
        $conn->close();
        exit(-1);
    }

    function getTop3FromDB($conn){
        $sql = "select * from Twitch_Entrega2;";
        $result = $conn->query($sql);
        $data = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    function setNew40GamesInDB($array, $conn){
        //Primero eliminamos los ultimos 40 registros para meter los del siguiente juego
        $sql = "DELETE FROM Prueba";

        // Ejecutar la sentencia SQL
        if ($conn->query($sql) === TRUE) {

            foreach($array as $entry){
                try {
                    $sql = "INSERT INTO Prueba (title, user, views, duracion, fecha_creacion) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssiss", $entry["title"], $entry["user_name"], $entry["view_count"], $entry["duration"], $entry["created_at"]);
                    $stmt->execute();
                    if ($stmt->affected_rows > 0) {
                        error_log("Registro añadido", 0);
                    } else {
                        echo "Error al introducir registros.";
                        closeConnectionAndExitDB();
                    }

                    $stmt->close();
                } catch (Exception $e) {
                    echo "Excepción capturada: " . $e->getMessage();
                    echo "<br>";
                }

            }
        } else {
            echo "Error al eliminar registros: " . $conn->error;
            closeConnectionAndExitDB();
        }
    }

    function getVideosFromUserFromDB($user, $conn){
        $sql = "SELECT user, COUNT(*) AS total_videos
        FROM Prueba
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
        FROM Prueba
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
        FROM Prueba
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
?>