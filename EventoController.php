<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Pergunta;
use App\Http\Requests\StorePerguntaRequest;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::all();

        return view('eventos.index', compact('eventos'));
    }


    /*
    * TICKET #002
    * Corrigido o problema N+1:
    * - Filtra perguntas pelo evento
    * - Carrega o usuário junto com as perguntas
    * - Ordena pelas mais recentes
    * - Pagina de 10 em 10
    */
    public function show($id)
    {
        $evento = Evento::findOrFail($id);

        $perguntas = Pergunta::with('user')
            ->where('evento_id', $evento->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('eventos.show', compact('evento', 'perguntas'));
    }


    /*
    * TICKET #001
    * Salva a pergunta vinculada ao evento e ao usuário logado.
    */
    public function storePergunta(StorePerguntaRequest $request, $id)
    {
        $evento = Evento::findOrFail($id);

        Pergunta::create([
            'evento_id' => $evento->id,
            'user_id' => auth()->id(),
            'texto' => $request->input('texto'),
            'status' => 'pendente',
        ]);

        return redirect()
            ->route('eventos.show', $evento->id)
            ->with('sucesso', 'Sua pergunta foi enviada com sucesso!');
    }
}