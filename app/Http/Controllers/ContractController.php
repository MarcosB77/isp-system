<?php

namespace App\Http\Controllers;

use App\Domains\Client\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function store(Request $request)
{
    $data = $request->validate([
        'client_id' => 'required|exists:clients,id',
        'plan_id'   => 'required', // Mudamos de plan_name para plan_id
        'active'    => 'boolean'
    ]);

    // Adicionamos as datas que o seu Model exige
    $data['starts_at'] = now();
    $data['due_day']   = 10; // Dia de vencimento padrão

    $contract = \App\Domains\Client\Models\Contract::create($data);

    return response()->json([
        'message' => 'Contrato ativado!',
        'data' => $contract
    ], 201);
}
}
