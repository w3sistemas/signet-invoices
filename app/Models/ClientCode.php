<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCode extends Model
{
    use HasFactory;

    public $fillable = [
        'id_nimbly',
        'code',
        'corporate_name',
        'document',
        'street',
        'number',
        'district',
        'zipcode',
        'city',
        'state',
        'email',
        'phone',
    ];
}
