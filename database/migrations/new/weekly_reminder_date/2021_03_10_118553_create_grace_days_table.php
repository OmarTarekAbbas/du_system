<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreategraceDaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        \DB::statement("ALTER TABLE `subscribers` ADD `grace_days` INT(10) NULL DEFAULT '0' AFTER `updated_at`;");
        \DB::statement("ALTER TABLE `unsubscribers` ADD `grace` INT(10) NULL DEFAULT NULL AFTER `updated_at`;");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	}

}
