<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Cart;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function generateReceipt($id)
    {
        $data = Transaction::with('carts')->find($id);
        $setting = Setting::first();
        $pdf = Pdf::loadView('pdf.receipt', compact('data', 'setting'));
        return $pdf->download('receipt_'.$data->name.'.pdf');
    }
}
