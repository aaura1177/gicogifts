<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    public function pdfBinary(Order $order): string
    {
        $order->load('items');

        return Pdf::loadView('pdfs.invoice', [
            'order' => $order,
            'legalLine' => config('gicogifts.legal_line'),
        ])
            ->setPaper('a4')
            ->output();
    }
}
