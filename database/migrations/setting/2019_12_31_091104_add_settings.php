<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('settings')->insert(
            array(
                array (
                    'key' => 'enable_approved',
                    'value' => '1',
                    'created_at' => '2019-12-31 10:50:04',
                    'updated_at' => '2019-12-31 08:54:06',
                )
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
