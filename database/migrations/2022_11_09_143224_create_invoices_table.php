<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_code')->nullable();
            $table->string('company_name')->nullable();
            $table->string('cnpj')->nullable();
            $table->integer('invoice')->unique()->nullable();
            $table->string('status')->nullable();
            $table->integer('qty')->nullable();
            $table->float('amount', 10,2)->nullable();
            $table->float('total', 10,2)->nullable();
            $table->float('paid', 10,2)->nullable();
            $table->dateTime('paid_date')->nullable();
            $table->dateTime('invoice_date')->nullable();
            $table->dateTime('invoice_duedate')->nullable();
            $table->integer('invoice_number')->nullable();
            $table->string('invoice_key')->nullable();
            $table->string('invoice_string')->nullable();
            $table->text('link_nfe')->nullable();
            $table->boolean('send_nimbly')->default(0);
            $table->dateTime('send_nimbly_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
