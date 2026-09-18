<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        return response()->json(Ticket::latest()->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'solicitante' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'categoria' => 'required|in:Hardware,Software,Red,Otro',
            'prioridad' => 'required|in:Baja,Media,Alta',
            'estado' => 'sometimes|required|in:Abierto,En proceso,Cerrado',
            'descripcion' => 'required|string',
        ]);

        $ticket = Ticket::create($validated);

        return response()->json($ticket, 201);
    }

    // NUEVO: Método show requerido agregado
    public function show($id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket no encontrado'], 404);
        }
        return response()->json($ticket, 200);
    }


    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return response()->json(['message' => 'No encontrado'], 404);

        $validated = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'solicitante' => 'sometimes|required|string|max:255',
            'correo' => 'sometimes|required|email|max:255',
            'categoria' => 'sometimes|required|in:Hardware,Software,Red,Otro',
            'prioridad' => 'sometimes|required|in:Baja,Media,Alta',
            'estado' => 'sometimes|required|in:Abierto,En proceso,Cerrado',
            'descripcion' => 'sometimes|required|string',
        ]);

        $ticket->update($validated);
        return response()->json($ticket, 200);
    }

    public function destroy($id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return response()->json(['message' => 'No encontrado'], 404);
        $ticket->delete();
        return response()->json(['message' => 'Eliminado'], 200);
    }
}