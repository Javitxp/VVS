<?php

namespace App\Infrastructure\Clients;

use App\Models\RegistredUser;
use App\Utilities\ErrorCodes;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use mysqli;

class DBClient
{
    public function connectToDB()
    {
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

    public function closeConnectionDB($conn)
    {
        $conn->close();
    }

    public function insertNewGame($conn, $new)
    {
        try {
            $json_data = json_encode($new);
            $sql = "INSERT INTO presentar (ID, datos) VALUES (?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $new["game_id"], $json_data);
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

    public function closeConnectionAndExitDB($conn)
    {
        $conn->close();
        exit(-1);
    }

    public function setNew40GamesInDB($array, $conn)
    {
        //Primero eliminamos los ultimos 40 registros para meter los del siguiente juego
        $sql = "DELETE FROM datos";

        // Ejecutar la sentencia SQL
        if ($conn->query($sql) === true) {

            foreach($array as $entry) {
                try {
                    $sql = "INSERT INTO datos (title, user, views, duracion, fecha_creacion) VALUES (?, ?, ?, ?, ?)";
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

    public function getVideosFromUserFromDB($user, $conn)
    {
        $sql = "SELECT user, COUNT(*) AS total_videos
        FROM datos
        WHERE user = '$user'
        GROUP BY user;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["total_videos"];
        } else {
            return -1;
        }
    }

    public function getSumViewsFromUserFromDB($user, $conn)
    {
        $sql = "SELECT user, SUM(views) AS total_views
        FROM datos
        WHERE user = '$user'
        GROUP BY user;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["total_views"];
        } else {
            return -1;
        }
    }

    public function getMostViewedFromUserFromDB($user, $conn)
    {
        $sql = "SELECT user, title, max(views) AS views_max, duracion, fecha_creacion
        FROM datos
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
        } else {
            return -1;
        }
    }

    public function getLast10($videoId)
    {
        $conn = $this->connectToDB();
        $sql = "SELECT * FROM presentar WHERE TIMESTAMPDIFF(MINUTE, Tiempo, NOW()) < 10 AND id = ? LIMIT 1;";
        $statement = $conn->prepare($sql);
        $statement->bind_param("i", $videoId);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return json_decode($row["datos"]);
        } else {
            return null;
        }
    }

    public function getSince($seconds, $videoId)
    {
        $conn = $this->connectToDB();
        $sql = "SELECT * FROM presentar WHERE TIMESTAMPDIFF(SECOND, Tiempo, NOW()) < ? AND id = ? LIMIT 1;";
        $statement = $conn->prepare($sql);
        $statement->bind_param("ii", $seconds, $videoId);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return json_decode($row["datos"]);
        } else {
            return null;
        }
    }

    public function checkGameId($gameId)
    {
        $count = 0;
        $conn = $this->connectToDB();
        try {
            $sql = "SELECT COUNT(*) FROM presentar WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $gameId);
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
    public function getAndInsertGameTopsOfTheTops($gameId, $name, $top40Videos)
    {
        $conn = $this->connectToDB();
        $topUser = $top40Videos[0];
        $user = $topUser["user_name"];
        $this->setNew40GamesInDB($top40Videos, $conn);
        $totalVideos = $this->getVideosFromUserFromDB($user, $conn);
        $sum = $this->getSumViewsFromUserFromDB($user, $conn);
        $dataMostViewed = $this->getMostViewedFromUserFromDB($user, $conn);

        $allData = array(
            "game_id" => $gameId,
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
    public function updateGameTopsOfTheTops($gameId, $name, $top40Videos)
    {
        $conn = $this->connectToDB();
        try {
            $sql = "DELETE FROM presentar WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $gameId);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            return;
        }
        $topUser = $top40Videos[0];
        $user = $topUser["user_name"];
        $this->setNew40GamesInDB($top40Videos, $conn);
        $totalVideos = $this->getVideosFromUserFromDB($user, $conn);
        $sum = $this->getSumViewsFromUserFromDB($user, $conn);
        $dataMostViewed = $this->getMostViewedFromUserFromDB($user, $conn);

        $allData = array(
            "game_id" => $gameId,
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

    public function getToken()
    {
        $token = DB::table('token')->first();

        return $token?->value;
    }

    public function replaceToken(String $value)
    {
        $token = DB::table('token')->first();

        if (!$token) {
            DB::table('token')->insert(['value' => $value]);
        } else {
            DB::table('token')->where('id', $token->id)->update(['value' => $value]);
        }

    }

    public function checkUsername($username)
    {
        try {
            return RegistredUser::where('username', $username)->exists();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function insertUser($username, $password)
    {
        try {
            $user = new RegistredUser();
            $user->username = $username;
            $user->password = Hash::make($password);
            $user->followedStreamers = json_encode([]);
            $user->save();
            return $user;
        } catch (Exception $exception) {
            throw new Exception("Error al crear el usuario.", ErrorCodes::USERS_500);
        }
    }

    /**
     * @throws Exception
     */
    public function getAllUsers()
    {
        try {
            $users = DB::table('registredUsers')
                ->select('username', DB::raw('JSON_UNQUOTE(followedStreamers) as followedStreamers'))
                ->get();
            foreach ($users as $user) {
                $user->followedStreamers = json_decode($user->followedStreamers);
            }
            return $users;
        } catch (Exception $e) {
            throw new Exception("Error al obtener la lista de usuarios.", ErrorCodes::USERS_500);
        }
    }


    public function findUserById($userId)
    {
        return RegistredUser::find($userId);
    }


    /**
     * @throws Exception
     */
    public function updateUserFollowedStreamers(RegistredUser $user, $followedStreamers)
    {
        try {
            $user->followedStreamers = $followedStreamers;
            $user->save();
            return $user;
        } catch (Exception $exception) {
            throw new Exception("Error del servidor al dejar de seguir al streamer.", 500);
        }
    }




}
