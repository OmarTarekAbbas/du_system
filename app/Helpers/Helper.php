<?php
namespace App\Helpers;
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

  

}
?>