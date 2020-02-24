<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUnsubscribersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('unsubscribers', function(Blueprint $table)
		{
			$table->foreign('activation_id', 'unsub_act_fk')->references('id')->on('activation')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('unsubscribers', function(Blueprint $table)
		{
			$table->dropForeign('unsub_act_fk');
		});
	}

}
