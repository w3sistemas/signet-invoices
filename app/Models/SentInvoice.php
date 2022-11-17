<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentInvoice extends Model
{
    use HasFactory;

    public $fillable = [
        'invoice_id',
        'sent',
        'paid_date',
        'paid',
        'id_nimbly_invoice',
        'status'
    ];
}
