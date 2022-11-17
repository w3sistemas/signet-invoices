<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsPaymentsSentInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sent_invoices', function (Blueprint $table) {
            $table->dateTime('paid_date')->nullable()->after('sent');
            $table->float('paid', 10, 2)->nullable()->after('paid_date');
            $table->integer('id_nimbly_invoice')->nullable()->after('paid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sent_invoices', function (Blueprint $table) {
            //
        });
    }
}
