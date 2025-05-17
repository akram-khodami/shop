<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Invoice;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['order' => function ($query) {

            return $query->where('user_id', auth()->id())->with('user');

        }])->paginate();

        return response()->json($invoices);
    }

}
