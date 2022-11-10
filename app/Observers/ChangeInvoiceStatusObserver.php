<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\SentInvoice;

class ChangeInvoiceStatusObserver
{
    public function updated(Invoice $invoice)
    {
        if($invoice->isDirty('status')) {
            SentInvoice::create([
                'invoice_id' => $invoice->id
            ]);
        }
    }
}
