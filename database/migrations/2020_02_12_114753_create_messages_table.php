<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('MTBody');
			$table->date('date');
			$table->string('MTURL');
			$table->string('ShortnedURL', 150)->nullable();
			$table->text('IsysURL', 65535)->nullable();
			$table->string('IsysResponse')->nullable();
			$table->boolean('status')->default(0);
			$table->integer('user_id')->unsigned()->index('messages_user_id_foreign');
			$table->integer('service_id')->unsigned()->index('messages_service_id_foreign');
			$table->timestamps();
			$table->string('time', 10)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('messages');
	}

}
