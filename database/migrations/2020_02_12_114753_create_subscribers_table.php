<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscribersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscribers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('activation_id')->unsigned()->index('sub_act_fk');
			$table->date('next_charging_date')->default('0000-00-00');
			$table->date('subscribe_date');
			$table->boolean('final_status')->default(1)->comment('0=not active , 1=active');
			$table->boolean('charging_cron')->default(0)->comment('0=  cronfail, 1= run success');
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
		Schema::drop('subscribers');
	}

}
