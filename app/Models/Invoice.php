<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public $fillable = [
        'abn',
        'customer_code',
        'company_name',
        'cnpj',
        'invoice',
        'status',
        'qty',
        'amount',
        'total',
        'paid',
        'paid_date',
        'invoice_date',
        'invoice_duedate',
        'invoice_number',
        'invoice_key',
        'invoice_string',
        'link_nfe',
        'send_nimbly',
        'id_nimbly_invoice',
        'ticket_id',
        'payload',
        'send_nimbly_date'
    ];
}
