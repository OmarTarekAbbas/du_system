<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('services', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title', 40);
			$table->string('service', 40);
			$table->string('lang', 5);
			$table->string('type', 30);
			$table->integer('operator_id')->unsigned()->index('services_operator_id_foreign');
			$table->timestamps();
			$table->integer('size')->default(800);
			$table->string('ExURL')->default('http://ivas.mobi');
			$table->string('service_id', 100)->nullable();
			$table->string('sender_name', 100)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('services');
	}

}
