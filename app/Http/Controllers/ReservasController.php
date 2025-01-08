<?php

namespace App\Http\Controllers;

use App\Models\Reservas;
use Carbon\Carbon;
use Illuminate\Http\Request;

date_default_timezone_set('America/Sao_Paulo');

class ReservasController extends Controller
{
    protected $modelReservas;

    public function __construct()
    {
        $this->modelReservas = new Reservas;
    }

    public function todosHorarios()
    {
        $horarios = $this->modelReservas->todosHorarios();

        return response()->json($horarios);
    }

    public function todosServicos()
    {
        $servicos = $this->modelReservas->todosServicos();

        return response()->json($servicos);
    }

    public function existeReserva(Request $request)
    {
        $request->validate([
            "data" => "required",
            "hora" => "required"
        ]);

        $inputs = $request->all();

        $dataAtual = Carbon::now();

        $data = isset($inputs["data"]) ? Carbon::create($inputs["data"]) : NULL;
        $hora = isset($inputs["hora"]) ? Carbon::create($inputs["hora"]) : NULL;


        if ($data->lt($dataAtual->toDateString())) {
            return response()->json(["error" => TRUE, "msg" => "A data não pode ser menor que a data de hoje"]);
        }

        if ($data->eq($dataAtual->toDateString()) && $hora->lt($dataAtual->toTimeString())) {
            return response()->json(["error" => TRUE, "msg" => "A hora não pode ser menor que a hora atual"]);
        }

        $existe = $this->modelReservas->existeDataEHora($inputs);

        if ($existe) {
            return response()->json(["error" => TRUE, "msg" => "Horario já reservado"]);
        }
    }

    public function cadastrar(Request $request)
    {
        $request->validate([
            "nome_reserva" => "required",
            "data" => "required",
            "hora" => "required",
            "servico" => "required",
            "usuarios_id" => "required",
            "barbearia_id" => "required"
        ]);

        $inputs = $request->all();

        $cadastrar = $this->modelReservas->cadastrar($inputs);

        if ($cadastrar->erro) {
            return response()->json(["error" => TRUE, "msg" => $cadastrar->msg]);
        }
    }
}