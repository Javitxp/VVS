<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopsOfTheTopsController extends Controller
{
    public function __construct()
    {
    }
    /**
     * Handle the incoming request.
     */
    public function getTopOfTheTops(Request $request): JsonResponse
    {
        // Verificar si se recibió una solicitud GET
        if (!$request->isMethod('get')) {
            return response()->json(['message' => 'Only GET requests'], 405);
        }

        // Incluir el archivo de utilidades
        include_once(__DIR__ . '/ApiController.php');
        $apiController = new ApiController();

        // Incluir el archivo de BBDD
        include_once(__DIR__ . '/DatabaseController.php');
        $databaseController = new DatabaseController();

        // Obtener el top 3 de juegos
        $top3_games = $apiController->getTop3Games();
        if ($top3_games === null) {
            return response()->json(['message' => 'Error al obtener top 3 juegos'], 500);
        }

        $topsOfTheTops = [];
        foreach ($top3_games as $game) {
            $id = $game["id"];
            $name = $game["name"];

            // Verificar si el juego existe en la base de datos
            $result = $databaseController->checkGameId($id);
            if ($result) {
                // Obtener datos según el parámetro 'since' si está presente, de lo contrario, obtener los últimos 10 registros
                $json = isset($request->since) ? $databaseController->getSince($request->since, $id) : $databaseController->getLast10($id);
                if ($json === null) {
                    $json = $databaseController->updateGame($id, $name);
                }
            } else {
                $json = $databaseController->getAndInsertGame($id, $name);
            }
            $topsOfTheTops[] = $json;
        }
        return response()->json($topsOfTheTops);
    }
}
