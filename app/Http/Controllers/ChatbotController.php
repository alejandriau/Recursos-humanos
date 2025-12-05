<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Persona;
use App\Models\Historial;
use App\Models\Puesto;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');
        Log::info("Mensaje recibido: " . $message);

        // Analizar la intención
        $intention = $this->analyzeIntention($message);
        Log::info("Intención detectada: ", $intention);

        // Si es una consulta de base de datos
        if ($intention['type'] === 'query') {
            $dbData = $this->queryDatabase($intention);
            Log::info("Datos de DB obtenidos: ", ['count' => $dbData ? count($dbData['data']) : 0]);

            if ($dbData && !empty($dbData['data'])) {
                $enhancedMessage = $this->buildEnhancedMessage($message, $dbData, $intention);
                Log::info("Mensaje mejorado enviado a DeepSeek");

                $response = $this->callDeepSeekAPI($enhancedMessage);
                return response()->json(['response' => $response]);
            } else {
                // No se encontraron datos
                $response = "No encontré información en la base de datos que coincida con tu búsqueda. ";
                $response .= "Puedes intentar con: \n";
                $response .= "- Un nombre o apellido diferente\n";
                $response .= "- Un número de cédula específico\n";
                $response .= "- Solicitar información general del sistema";

                return response()->json(['response' => $response]);
            }
        }

        // Consulta general
        $response = $this->callDeepSeekAPI($message);
        return response()->json(['response' => $response]);
    }

    private function analyzeIntention(string $message): array
    {
        $message = strtolower(trim($message));

        Log::info("Analizando intención para: " . $message);

        // Detectar consultas por APELLIDO
        if (preg_match('/(personas?|gente|empleados?).*apellido\s+(\w+)/i', $message, $matches)) {
            Log::info("Detectado apellido: " . $matches[2]);
            return [
                'type' => 'query',
                'entity' => 'persona',
                'search_type' => 'apellido',
                'search_term' => $matches[2],
                'original_message' => $message
            ];
        }

        // Detectar consultas por NOMBRE
        if (preg_match('/(información|datos|consulta|buscar).*(persona|empleado)\s+(\w+)/i', $message, $matches)) {
            Log::info("Detectado nombre: " . $matches[3]);
            return [
                'type' => 'query',
                'entity' => 'persona',
                'search_type' => 'nombre',
                'search_term' => $matches[3],
                'original_message' => $message
            ];
        }

        // Detectar consultas por CI
        if (preg_match('/(ci|cedula|cédula)\s+(\w+)/i', $message, $matches)) {
            Log::info("Detectado CI: " . $matches[2]);
            return [
                'type' => 'query',
                'entity' => 'persona',
                'search_type' => 'ci',
                'search_term' => $matches[2],
                'original_message' => $message
            ];
        }

        // Detectar consultas generales de personas
        if (preg_match('/(personas?|empleados?|trabajadores?).*(sistema|base de datos|existen|hay|listar)/i', $message)) {
            Log::info("Detectada consulta general de personas");
            return [
                'type' => 'query',
                'entity' => 'persona',
                'search_type' => 'general',
                'search_term' => null,
                'original_message' => $message
            ];
        }

        Log::info("Intención general detectada");
        return [
            'type' => 'general',
            'entity' => null,
            'search_type' => null,
            'search_term' => null,
            'original_message' => $message
        ];
    }

    private function queryDatabase(array $intention): ?array
    {
        try {
            Log::info("Consultando BD para: ", $intention);

            if ($intention['entity'] === 'persona') {
                return $this->queryPersonaData($intention);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error en queryDatabase: ' . $e->getMessage());
            return null;
        }
    }

private function queryPersonaData(array $intention): ?array
{
    try {
        Log::info("Consultando BD para: ", $intention);

        $query = Persona::where('estado', 1);

        switch ($intention['search_type']) {
            case 'nombre_completo':
                Log::info("Buscando por nombre completo: " . $intention['search_term']);
                $terms = explode(' ', $intention['search_term']);

                if (count($terms) >= 2) {
                    $query->where(function($q) use ($terms) {
                        $q->where('nombre', 'LIKE', '%' . $terms[0] . '%')
                          ->where('apellidoPat', 'LIKE', '%' . $terms[1] . '%');
                    });
                } else {
                    $query->where('nombre', 'LIKE', '%' . $intention['search_term'] . '%')
                          ->orWhere('apellidoPat', 'LIKE', '%' . $intention['search_term'] . '%')
                          ->orWhere('apellidoMat', 'LIKE', '%' . $intention['search_term'] . '%');
                }
                break;

            case 'apellido':
                Log::info("Buscando por apellido: " . $intention['search_term']);
                $query->where('apellidoPat', 'LIKE', '%' . $intention['search_term'] . '%')
                     ->orWhere('apellidoMat', 'LIKE', '%' . $intention['search_term'] . '%');
                break;

            case 'ci':
                Log::info("Buscando por CI: " . $intention['search_term']);
                $query->where('ci', 'LIKE', '%' . $intention['search_term'] . '%');
                break;

            case 'general':
                Log::info("Búsqueda general de personas");
                break;
        }

        $personas = $query->limit(10)->get();
        Log::info("Personas encontradas: " . $personas->count());

        if ($personas->count() > 0) {
            return [
                'type' => 'success',
                'entity' => 'persona',
                'search_type' => $intention['search_type'],
                'data' => $personas->toArray()
            ];
        }

        return null;

    } catch (\Exception $e) {
        Log::error('Error en queryPersonaData: ' . $e->getMessage());
        return null;
    }
}

    private function buildEnhancedMessage(string $originalMessage, array $dbData, array $intention): string
    {
        $context = "El usuario preguntó: \"{$originalMessage}\"\n\n";
        $context .= "INFORMACIÓN ENCONTRADA EN LA BASE DE DATOS:\n\n";

        if ($dbData['entity'] === 'persona') {
            $personas = $dbData['data'];

            if (count($personas) === 1) {
                $p = $personas[0];
                $context .= "PERSONA ENCONTRADA:\n";
                $context .= "🔹 Nombre: {$p['nombre']} {$p['apellidoPat']} {$p['apellidoMat']}\n";
                $context .= "🔹 CI: {$p['ci']}\n";
                $context .= "🔹 Fecha Nacimiento: {$p['fechaNacimiento']}\n";
                $context .= "🔹 Sexo: {$p['sexo']}\n";
                $context .= "🔹 Teléfono: {$p['telefono']}\n";
                $context .= "🔹 Fecha Ingreso: {$p['fechaIngreso']}\n";

            } else {
                $context .= "SE ENCONTRARON " . count($personas) . " PERSONAS:\n";
                foreach ($personas as $index => $p) {
                    $context .= ($index + 1) . ". {$p['nombre']} {$p['apellidoPat']} {$p['apellidoMat']} (CI: {$p['ci']})\n";
                }
            }
        }

        $context .= "\nINSTRUCCIONES: Responde de manera natural y útil basándote en esta información. ";
        $context .= "Si hay múltiples resultados, sugiere cómo refinar la búsqueda. ";
        $context .= "Sé preciso con los datos proporcionados.";

        Log::info("Contexto construido: " . substr($context, 0, 200) . "...");

        return $context;
    }

    private function callDeepSeekAPI(string $message): string
    {
        try {
            Log::info("Llamando a DeepSeek API");

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.deepseek.com/v1/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un asistente de recursos humanos de la Gobernación. Responde de forma clara y útil usando la información proporcionada. Sé específico con los datos.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $apiResponse = trim($response['choices'][0]['message']['content'] ?? 'No response from API');
                Log::info("Respuesta de DeepSeek: " . substr($apiResponse, 0, 100) . "...");
                return $apiResponse;
            }

            Log::error("Error en API DeepSeek: " . $response->status());
            return 'Error al conectar con el servicio. Por favor intenta nuevamente.';

        } catch (\Exception $e) {
            Log::error('DeepSeek API Exception: ' . $e->getMessage());
            return 'Lo siento, ocurrió un error al procesar tu solicitud.';
        }
    }
}
