<?php
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
/*
|--------------------------------------------------------------------------
| ISP System API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Clientes
    Route::apiResource('clients', ClientController::class);
    Route::post('clients/{client}/suspend',  [ClientController::class, 'suspend']);
    Route::post('clients/{client}/activate', [ClientController::class, 'activate']);

    Route::apiResource('contracts', ContractController::class); 

    // Chamados de suporte
    Route::post('clients/{client}/tickets', [TicketController::class, 'store']);
    Route::post('tickets/{ticket}/resolve',  [TicketController::class, 'resolve']);
   
 
    
    // --------------------------------
    // Faturas
    Route::get('invoices',                [InvoiceController::class, 'index']);
    Route::post('invoices/{invoice}/pay', [InvoiceController::class, 'pay']);
});
