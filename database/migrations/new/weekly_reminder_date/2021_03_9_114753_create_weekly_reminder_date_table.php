<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWeeklyReminderDateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activation', function(Blueprint $table)
		{
			\DB::statement("ALTER TABLE `subscribers` ADD `weekly_reminder_date` DATE NULL DEFAULT NULL AFTER `subscribe_date`;");
            
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
