<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;
class Helper {

    public static function time() {

        $time = array(
            ''=>'',
            '12:00'=>'12:00 PM',
            '18:00'=>'6:00 PM',
            '21:00'=>'9:00 PM'
        );

        return $time ;

    }

    public static function setting()
    {
        $data = DB::table('settings')->where('key', 'like', '%' . 'enable_approved' . '%')->first();
        return $data ? $data->value : 0;
    }



}
?>
