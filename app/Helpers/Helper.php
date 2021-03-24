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

    public static function emails()
    {
        $email_array = array(
            "emad@ivas.com.eg",
            "dalia.soliman@ivas.com.eg",
            "sayed@ivas.com.eg",
            "raafat.ahmed@ivas.com.eg",
            "upload@ivas.com.eg",
            "sherif.mohamed@ivas.com.eg",
            "Tosson@ivas.com.eg",
        );

        return $email_array;
    }

}
?>
