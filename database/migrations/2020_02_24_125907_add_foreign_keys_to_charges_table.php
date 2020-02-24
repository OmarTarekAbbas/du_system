<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToChargesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('charges', function(Blueprint $table)
		{
			$table->foreign('subscriber_id', 'charge_sub_fk')->references('id')->on('subscribers')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('charges', function(Blueprint $table)
		{
			$table->dropForeign('charge_sub_fk');
		});
	}

}
