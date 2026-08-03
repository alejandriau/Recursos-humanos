<?php

namespace App\Http\Controllers;

use App\Services\EventoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function __construct(private EventoService $eventoService) {}

    public function marcarBienvenidaVista(Request $request): JsonResponse
    {
        $this->eventoService->marcarBienvenidaVista($request->user());
        return response()->json(['success' => true]);
    }
}
