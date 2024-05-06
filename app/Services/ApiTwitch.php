<?php

namespace App\Services;

use App\Infrastructure\Clients\DBClient;
use App\Infrastructure\Controllers\ApiController;
use App\Infrastructure\Clients\ApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

require_once __DIR__ . '/../Infrastructure/Controllers/ApiController.php';
require_once __DIR__ . '/../Infrastructure/Controllers/DBClient.php';



class ApiTwitch
{
    public function getUsers(Request $request): JsonResponse
    {
        // Verificar si se recibió una solicitud GET
        if (!$request->isMethod('get')) {
            return response()->json(['message' => 'Only GET requests'], 405);
        }

        // Verificar si se proporcionó el parámetro id
        if (!$request->has('id')) {
            return response()->json(['message' => 'Parameter id required'], 400);
        }

        // Configurar url y autorización y client-id en el encabezado
        $url = 'https://api.twitch.tv/helix/users?id=' . $request->input('id');

        $apiClient = new ApiClient();

        $headers = [
            'Authorization' => 'Bearer sga6x1z0df1pq1mskps75mjmhwa3p4',
            'Client-Id' => $apiClient->getToken()
        ];

        // Realizar la solicitud HTTP
        $response = Http::withHeaders($headers)->get($url);

        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            $user_data = $response->json();

            // Verificar si hay datos disponibles
            if (isset($user_data['data']) && count($user_data['data']) > 0) {
                // Devolver la información del usuario en formato JSON
                return response()->json($user_data['data'][0]);
            } else {
                return response()->json(['message' => 'There is no user with id: ' . $request->input('id')], 404);
            }
        } else {
            return response()->json(['message' => 'Error al realizar la solicitud: ' . $response->status()], 500);
        }
    }

    public function getStreams(): JsonResponse
    {
        //Configurar url y autorizacion y client-id en header
        $url = 'https://api.twitch.tv/helix/streams';

        $apiClient = new ApiClient();

        $headers = [
            'Authorization' => 'Bearer sga6x1z0df1pq1mskps75mjmhwa3p4',
            'Client-Id' => $apiClient->getToken()
        ];

        // Realizar la solicitud HTTP
        $response = Http::withHeaders($headers)->get($url);

        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            $streams_data = $response->json();

            // Verificar si hay datos disponibles
            if (isset($streams_data['data'])) {
                // Obtener la información necesaria (nombre del usuario y título del stream)
                $stream_info = collect($streams_data['data'])->map(function ($stream) {
                    return [
                        'title' => $stream['title'],
                        'user_name' => $stream['user_name']
                    ];
                });

                // Devolver la información en formato JSON
                return response()->json($stream_info);
            } else {
                return response()->json(['message' => 'No se encontraron datos de streams.'], 404);
            }
        } else {
            return response()->json(['message' => 'Error al realizar la solicitud: ' . $response->status()], 500);
        }
    }
    public function getTopOfTheTops(Request $request): JsonResponse
    {
        $apiController = new ApiController();
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
            $databaseController = new DBClient();
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
