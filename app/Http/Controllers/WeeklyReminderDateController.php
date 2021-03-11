<?php

namespace App\Http\Controllers;

use App\Activation;
use App\Subscriber;
use App\LogMessage;
use App\Charge;
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

    public function weekly_statistics(Request $request)
    {

        if($request->service){
            $total_today_charges = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.charging_date',date('Y-m-d'))
            ->where('activation.serviceid',$request->service)
            ->count();

            $charges_status_success_today = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.charging_date',date('Y-m-d'))
            ->where('charges.status_code',"0")
            ->where('activation.serviceid',$request->service)
            ->count();

            $charges_status_fail_today = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.charging_date',date('Y-m-d'))
            ->whereNotIn('charges.status_code',['503 - product already purchased!',0])
            ->where('activation.serviceid',$request->service)
            ->count();

            $get_all_subscribers = Subscriber::select('*')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('activation.serviceid',$request->service)
            ->count();

            $log_messages_table_today = LogMessage::whereDate('created_at',date('Y-m-d'))->where('service',$request->service)->where('message_type',"Weekly_Reminder")->count();

        }else{
            $total_today_charges = Charge::where('charging_date',date('Y-m-d'))->count();

            $charges_status_success_today = Charge::where('charging_date',date('Y-m-d'))->where('status_code',"0")->count();

            $charges_status_fail_today = Charge::where('charging_date',date('Y-m-d'))->whereNotIn('status_code',['503 - product already purchased!',0])->count();

            $get_all_subscribers = Subscriber::count();

            $log_messages_table_today = LogMessage::whereDate('created_at',date('Y-m-d'))->where('message_type',"Weekly_Reminder")->count();

        }
        $data['data'] = [
            'Total Today Charges'                => $total_today_charges,
            'Total Today Charges Status Success ' => $charges_status_success_today,
            'Total Today Charges Status Fail'    => $charges_status_fail_today,
            'Total Subscribers'                  => $get_all_subscribers,
            'Totel Weekly Reminder'     => $log_messages_table_today
        ];
        return response()->json( [$data] );
    }

    public function weekly_statistics_excel(Request $request)
    {
        if($request->service){

            $total_today_charges = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.charging_date',date('Y-m-d'))
            ->where('activation.serviceid',$request->service)
            ->count();

            $charges_status_success_today = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.charging_date',date('Y-m-d'))
            ->where('charges.status_code',"0")
            ->where('activation.serviceid',$request->service)
            ->count();

            $charges_status_fail_today = Charge::select('*','charges.id as charge_id','charges.status_code as charge_status_code')
            ->join('subscribers','subscribers.id','=','charges.subscriber_id')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('charges.charging_date',date('Y-m-d'))
            ->whereNotIn('charges.status_code',['503 - product already purchased!',0])
            ->where('activation.serviceid',$request->service)
            ->count();

            $get_all_subscribers = Subscriber::select('*')
            ->join('activation','subscribers.activation_id','=','activation.id')
            ->where('activation.serviceid',$request->service)
            ->count();

            $log_messages_table_today = LogMessage::whereDate('created_at',date('Y-m-d'))->where('service',$request->service)->where('message_type',"Weekly_Reminder")->count();

        }else{
            $total_today_charges = Charge::where('charging_date',date('Y-m-d'))->count();

            $charges_status_success_today = Charge::where('charging_date',date('Y-m-d'))->where('status_code',"0")->count();

            $charges_status_fail_today = Charge::where('charging_date',date('Y-m-d'))->whereNotIn('status_code',['503 - product already purchased!',0])->count();

            $get_all_subscribers = Subscriber::count();

            $log_messages_table_today = LogMessage::whereDate('created_at',date('Y-m-d'))->where('message_type',"Weekly_Reminder")->count();

        }

        \Excel::create('WeeklyStatisticsExcel-'.Carbon::now()->toDateString(), function($excel) use ($total_today_charges,$charges_status_success_today,$charges_status_fail_today,$get_all_subscribers,$log_messages_table_today) {
            $excel->sheet('Excel', function($sheet) use ($total_today_charges,$charges_status_success_today,$charges_status_fail_today,$get_all_subscribers,$log_messages_table_today) {
                $sheet->loadView('backend.weekly_statistics_excel.weekly_statistics_excel')->with("total_today_charges", $total_today_charges)->with("charges_status_success_today", $charges_status_success_today)->with("charges_status_fail_today", $charges_status_fail_today)->with("get_all_subscribers", $get_all_subscribers)->with("log_messages_table_today", $log_messages_table_today);
            });
        })->export('xlsx');

    }

}
