<?php

namespace App\Http\Controllers;

use App\Activation;
use App\Subscriber;
use App\LogMessage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WeeklyReminderDateController extends Controller
{

    public function weekly_reminder_date()
    {
        $today = Carbon::now()->format('Y-m-d');
        $subscribers = Subscriber::where('subscribers.weekly_reminder_date', $today)
        ->select('subscribers.*')
        ->orderBy('id', 'ASC')
        ->get();


        $subscribers_count = Subscriber::where('subscribers.weekly_reminder_date', $today)
        ->orderBy('id', 'ASC')
        ->count();


        if( $subscribers_count > 0 ){  // run

            $email = "emad@ivas.com.eg";
            $subject = "Weekly Reminder Date Cron Run Schedule for " . Carbon::now()->format('Y-m-d');
            $this->sendMail($subject, $email);

            foreach ($subscribers as $sub) {
                $activation = Activation::findOrFail($sub->activation_id);
                $serviceid = $activation->serviceid;
                $msisdn = $activation->msisdn;
                $message = WEEKLY_REMINDER_MESSAGE[$serviceid];
                $result =  $this->send_message($msisdn, $serviceid, $message);
                if ($result == "1"){
                    $sub->weekly_reminder_date = Carbon::parse($sub->weekly_reminder_date)->addDays(7);
                    $sub->save();
                }

            }

            echo "Du Weekly Reminder Date for toady " . $today . " Is Done";

        }else{
            echo "There is no  Weekly Reminder Date for toady " . $today ;

        }
    }

    public function sendMail($subject, $email, $Message = null)
    {

        // send mail
        $message = '<!DOCTYPE html>
					<html lang="en-US">
						<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						</head>
						<body>
							<h2>' . $subject . '</h2>



						</body>
					</html>';

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: DU SYSTEM';

        @mail($email, $subject, $message, $headers);
    }

    public function send_message($phoneNumber,$serviceid,$message)
    {
        // Du sending welcome message
        $message_type = "Weekly_Reminder";
        $URL = "http://41.33.167.14:2080/~smsdu/du_send_message";
        $param = "phone_number=$phoneNumber&message=$message";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result == "1") {
            $status = 1;
        } else {
            $status = 0;
        }


        $logmes = new LogMessage();
        $logmes->service       = $serviceid;
        $logmes->msisdn        = $phoneNumber;
        $logmes->message       = $message;
        $logmes->message_type  = $message_type;
        $logmes->status  = $status;
        $logmes->save();
        return $result ;

    }
}
