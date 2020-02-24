<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChargesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('charges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('subscriber_id')->unsigned()->index('charge_sub_fk');
			$table->text('billing_request', 65535);
			$table->text('billing_response', 65535);
			$table->string('status_code', 100);
			$table->date('charging_date');
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
		Schema::drop('charges');
	}

}
