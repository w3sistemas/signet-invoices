<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('id_nimbly')->nullable();
            $table->integer('code')->nullable()->unique();
            $table->text('corporate_name')->nullable();
            $table->string('document')->nullable();
            $table->string('street')->nullable();
            $table->integer('number')->nullable();
            $table->string('district')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
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
        Schema::dropIfExists('client_codes');
    }
}
