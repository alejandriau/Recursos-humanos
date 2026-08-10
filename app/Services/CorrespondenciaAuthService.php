<?php
// app/Services/CorrespondenciaAuthService.php
/*namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Persona;

class CorrespondenciaAuthService
{
    protected string $url = 'http://correspondencia.gobernaciondecochabamba.bo/Restserver/singin';
    protected string $token = 'servidoresgadc12345';

    public function autenticar(string $usuario, string $password): ?array
    {
        try {
            $response = Http::timeout(6)->post($this->url, [
                'login'    => $usuario,
                'password' => $password,
                'token'    => $this->token,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $body = $response->json();

        if (($body['code'] ?? null) != 200
            || ($body['status'] ?? null) !== 'success'
            || empty($body['data'][0])) {
            return null;
        }

        return $body['data'][0];
    }
    // app/Services/CorrespondenciaAuthService.php

    public function autenticarEmpleado(string $usuario, string $password): ?User
    {
        $datos = $this->autenticar($usuario, $password); // el método que ya hicimos antes

        if (!$datos) {
            return null;
        }

        $ci = $datos['ci'];
        $persona = Persona::where('ci', $ci)->first();

        if (!$persona) {
            return null;
        }

        $user = $persona->user;

        if (!$user) {
            $user = User::create([
                'name'     => trim("{$datos['nombres']} {$datos['paterno']} {$datos['materno']}"),
                'email'    => null,
                'usuario'  => $datos['usuario'],
                'ci'       => $ci,
                'password' => Hash::make(Str::random(40)),
                'origen'   => 'correspondencia',
            ]);

            $persona->user_id = $user->id;
            $persona->save();
            $user->assignRole('empleado');
        }

        return $user;
    }
}