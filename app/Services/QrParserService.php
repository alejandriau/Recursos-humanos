<?php

namespace App\Services;

use App\Models\Persona;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class QrParserService
{
    /**
     * Devuelve la Persona a partir del texto crudo del QR.
     * Soporta:
     *   1) Formato viejo (texto plano del GAD Cochabamba con "CI: 4523366")
     *   2) Formato nuevo (JSON firmado con token)
     *
     * @return array{persona: Persona|null, error: string|null}
     */
    public function resolverPersona(string $qrRaw): array
    {
        $qrRaw = trim($qrRaw);

        if ($qrRaw === '') {
            return ['persona' => null, 'error' => 'QR vacío'];
        }

        // 1) Intentar formato NUEVO (JSON con token firmado)
        $resultadoNuevo = $this->intentarFormatoNuevo($qrRaw);
        if ($resultadoNuevo['persona'] !== null || $resultadoNuevo['error'] === 'token_invalido') {
            return $resultadoNuevo;
        }

        // 2) Fallback: formato VIEJO (texto plano)
        return $this->intentarFormatoViejo($qrRaw);
    }

    /**
     * Formato NUEVO: {"token":"eyJpdiI6..."}
     */
    private function intentarFormatoNuevo(string $qrRaw): array
    {
        $data = json_decode($qrRaw, true);

        if (!is_array($data) || !isset($data['token'])) {
            // No es formato nuevo, no es error => dejamos que pruebe el viejo
            return ['persona' => null, 'error' => null];
        }

        try {
            $payload = Crypt::decryptString($data['token']);
            $payload = json_decode($payload, true);

            if (!isset($payload['persona_id'])) {
                return ['persona' => null, 'error' => 'token_invalido'];
            }

            $persona = Persona::find($payload['persona_id']);

            if (!$persona) {
                return ['persona' => null, 'error' => 'persona_no_encontrada'];
            }

            return ['persona' => $persona, 'error' => null];
        } catch (\Throwable $e) {
            Log::warning('QR nuevo inválido', ['error' => $e->getMessage()]);
            return ['persona' => null, 'error' => 'token_invalido'];
        }
    }

    /**
     * Formato VIEJO: texto plano con "CI: 4523366"
     */
    private function intentarFormatoViejo(string $qrRaw): array
    {
        // Busca "CI:" seguido de dígitos (tolera espacios, saltos de línea, mayúsculas)
        if (!preg_match('/CI[:\s]*([0-9]{4,15})/i', $qrRaw, $matches)) {
            return ['persona' => null, 'error' => 'formato_invalido'];
        }

        $ci = $matches[1];
        $persona = Persona::where('ci', $ci)->first();

        if (!$persona) {
            return ['persona' => null, 'error' => "CI {$ci} no encontrado"];
        }

        return ['persona' => $persona, 'error' => null];
    }

    /**
     * Genera un token firmado para el QR NUEVO.
     * Úsalo cuando renueves la credencial de una persona.
     */
    public function generarToken(Persona $persona): string
    {
        $payload = json_encode([
            'persona_id' => $persona->id,
            'ci'         => $persona->ci,
            'emitido'    => now()->timestamp,
        ]);

        return Crypt::encryptString($payload);
    }
}
