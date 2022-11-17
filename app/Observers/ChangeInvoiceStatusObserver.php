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
                'invoice_id' => $invoice->id,
                'paid_date' => $invoice->paid_date,
                'paid' => $invoice->paid,
                'id_nimbly_invoice' => $invoice->id_nimbly_invoice,
                'status' => $invoice->status
            ]);
        }
    }
}
