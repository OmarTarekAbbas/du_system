<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activation', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('trxid', 400)->nullable();
			$table->string('msisdn', 20)->nullable();
			$table->string('serviceid', 30)->nullable();
			$table->string('plan', 30)->nullable();
			$table->string('price', 30)->nullable();
			$table->timestamps();
			$table->text('du_request', 65535)->nullable();
			$table->text('du_response', 65535)->nullable();
			$table->string('status_code', 100)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activation');
	}

}
