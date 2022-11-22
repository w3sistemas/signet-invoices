<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsTicketInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('our_number')->nullable()->after('invoice_string');
            $table->float('late_payment', 10,2)->nullable()->after('our_number');
            $table->integer('bank')->nullable()->after('late_payment');
            $table->float('discounts', 10, 2)->nullable()->after('bank');
            $table->float('increase', 10, 2)->nullable()->after('discounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
