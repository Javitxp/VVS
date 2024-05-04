<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StreamsController extends Controller
{
    public function __construct()
    {
    }
    /**
     * Handle the incoming request.
     */
    public function getStreams(Request $request): JsonResponse
    {
        // Verificar si se recibió una solicitud GET
        if (!$request->isMethod('get')) {
            return response()->json(['message' => 'Only GET requests'], 405);
        }

        //Configurar url y autorizacion y client-id en header
        $url = 'https://api.twitch.tv/helix/streams';

        $headers = [
            'Authorization' => 'Bearer sga6x1z0df1pq1mskps75mjmhwa3p4',
            'Client-Id' => 'rxbua83lt6p4yqdig92dvsoicmdi87'
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
}
