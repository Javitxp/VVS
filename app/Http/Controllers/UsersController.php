<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UsersController extends Controller
{
    public function __construct()
    {
    }
    /**
     * Handle the incoming request
     */
    public function getStreams(Request $request): JsonResponse
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

        $headers = [
            'Authorization' => 'Bearer sga6x1z0df1pq1mskps75mjmhwa3p4',
            'Client-Id' => 'rxbua83lt6p4yqdig92dvsoicmdi87'
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
}
