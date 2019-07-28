<?php
if( !isset($_SESSION) ) session_start();
include_once("sms/SendSMS_SoapClient.php");

$json_params = file_get_contents("php://input");
$user_data = json_decode($json_params);

if( $user_data->status == null || $user_data->phone_entry == null ) {
    echo "Err";
    exit;
}

// print_r($user_data['gender']);
// exit;



switch($user_data->status) {
    case 0 :
        $_SESSION['phone_entry'] = $user_data->phone_entry;
        $_SESSION['verfied_code'] = rand(1000, 9999);
        $sms_text = "کد شما در قلم: $_SESSION[verfied_code]";
        if(SendSMS($sms_text, $_SESSION['phone_entry'])) {
            echo "sms-success";
            exit;
        } else {
            echo "sms-failed";
            exit;
        }
        break;
    case 1 :
        if($user_data->verify_code == null) {
            echo "Err";
            return 0;
        }
        if($_SESSION['verfied_code'] == $user_data->verify_code) {
            $_SESSION['verfied_phone'] = $_SESSION['phone_entry'];
            unset($_SESSION['verfied_code']);
            // Check User Register
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://account.modir.app/ghalams?phone=".$_SESSION['phone_entry'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err) {
                if($response == "[]") {
                    echo "signup";
                    exit;
                } else {
                    echo "signin";
                    exit;
                }
            } else {
                echo "Err";
                exit; 
            }
        } else {
            echo "invalid";
            exit;
        }
        break;
    case 2:
        $phone = $_SESSION['verfied_phone'];
        $fullname = $user_data->fullname;
        $major = $user_data->major;
        $gender = ($user_data->gender == 'male' ? 1 : 2);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://account.modir.app/ghalams/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"phone\": \"$phone\",\"fullname\": \"$fullname\",\"major\": \"$major\",\"gender\": \"$gender\"}",
            CURLOPT_HTTPHEADER => array(
            "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if (!$err) {
            $sms_text = "$fullname عزیز ثبت نام شما در قلم با موفقیت انجام شد.";
            SendSMS($sms_text, $_SESSION['phone_entry']);
            echo "registered";
            exit;
        } else {
            echo "Err";
            exit;
        }
        break;
    default :
        echo "Err";
}
unset($_SESSION['phone_entry']);



