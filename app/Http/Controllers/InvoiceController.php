<?php
namespace App\Http\Controllers;

use App\Domains\Billing\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Invoice::with('client')->latest()->paginate(20)
        );
    }

    public function pay(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate([
            'method' => ['required', 'in:pix,boleto,cartao,dinheiro'],
        ]);

        if ($invoice->isPaid()) {
            return response()->json(['message' => 'Fatura já foi paga.'], 422);
        }

        $invoice->markAsPaid($request->method);

        return response()->json(['message' => 'Pagamento registrado com sucesso.']);
    }
}
