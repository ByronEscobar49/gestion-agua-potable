<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class FichaClienteController extends Controller
{
    public function __invoke(Cliente $cliente): View
    {
        $this->authorize('view', $cliente);

        $cliente = Cliente::query()
            ->conEstadoDeCuenta()
            ->with(['contadores.predio.sector', 'contadores.paja'])
            ->findOrFail($cliente->getKey());

        $venceMasAntigua = $cliente->getAttribute('vence_mas_antigua');

        return view('clientes.ficha-impresion', [
            'cliente' => $cliente,
            'venceMasAntigua' => $venceMasAntigua
                ? Carbon::parse($venceMasAntigua)
                : null,
        ]);
    }
}