<?php
/*
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    // Muestra el formulario de login
    public function show()
    {
        return view('auth.login');
    }

    // Procesa el login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credenciales = $request->only('email', 'password');

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
            return redirect()->intended(route('homeadm')); // ✅ redirige al dashboard
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son válidas.',
        ])->withInput();
    }

    // Cierra sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function loginApi(Request $request)
    {
        try {

            $response = Http::post(
                'http://correspondencia.gobernaciondecochabamba.bo/Restserver/singin',
                [
                    'login' => $request->usuario,
                    'password' => $request->password,
                    'token' => 'servidoresgadc12345'
                ]
            );

            return response()->json($response->json());

        } catch (\Exception $e) {

            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);

        }
    }


}
