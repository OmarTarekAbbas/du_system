<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('trxid', 400)->nullable();
            $table->string('msisdn', 30)->nullable();
            $table->string('serviceid', 30)->nullable();
            $table->string('plan', 30)->nullable();
            $table->string('price', 30)->nullable();
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
        Schema::dropIfExists('activation');
    }
}
