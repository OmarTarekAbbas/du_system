<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSubscribersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subscribers', function(Blueprint $table)
		{
			$table->foreign('activation_id', 'sub_act_fk')->references('id')->on('activation')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subscribers', function(Blueprint $table)
		{
			$table->dropForeign('sub_act_fk');
		});
	}

}
