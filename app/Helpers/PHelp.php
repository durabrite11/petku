<?php

use App\Models\Member;
use App\Models\Notification;
use App\Models\Topup;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\PConstant;

function formatCurrency($nominal)
{
    $result = "Rp " . number_format($nominal, 2, ',', '.');
    return $result;
}

function statusBadgeTopup($status)
{
    $class= "badge-warning";
    if ($status == PConstant::TOPUP_STATUS_PENDING) {
        $class= "badge-warning";
    } elseif ($status == PConstant::TOPUP_STATUS_APPROVED) {
        $class= "badge-success";
    } else {
        $class= "badge-danger";
    }
    return "<span class='badge $class'>$status</span>";
}


function statusBadgeTransaction($status)
{
    $class= "badge-warning";
    if ($status == PConstant::TRANSACTION_STATUS_SUCCESS) {
        $class= "badge-warning";
    } elseif ($status == PConstant::TRANSACTION_STATUS_COMPLETED) {
        $class= "badge-success";
    } else {
        $class= "badge-danger";
    }
    return "<span class='badge $class'>$status</span>";
}

function nextTopupCode()
{
    $topupModel = Topup::orderBy("id", "desc")->first();
    $currentId = 1;
    if ($topupModel != null) {
        $currentId = $topupModel->id+1;
    }
    $code = str_pad($currentId, "6", "0", STR_PAD_LEFT);

    return "TP".$code;
}
function nextTransactionCOde()
{
    $model = Transaction::orderBy("id", "desc")->first();
    $currentId = 1;
    if ($model != null) {
        $currentId = $model->id+1;
    }
    $code = str_pad($currentId, "6", "0", STR_PAD_LEFT);

    return "TRS".$code;
}
function sendNotification($memberIdTo, $title, $description)
{
    $member = Member::find($memberIdTo);
    if ($member) {
        $model = new Notification();
        $model->member_id = $memberIdTo;
        $model->title = $title;
        $model->description = $description;
        $model->save();

        sendPushNotification($member->fcm_token, $title, $description);
    }
}
function nextWithdrawCode()
{
    $withdrawModel = Withdraw::orderBy("id", "desc")->first();
    $currentId = 1;
    if ($withdrawModel != null) {
        $currentId = $withdrawModel->id+1;
    }
    $code = str_pad($currentId, "6", "0", STR_PAD_LEFT);

    return "WD".$code;
}
function sendPushNotification($fcmToken, $title, $description)
{
    $url = 'https://fcm.googleapis.com/fcm/send';

    $serverKey = 'AAAAWmAgiWo:APA91bFFTXHoZY4lJcIDd1m7V9v1lfpv3gqpp9P3nPdNa-B_2dLcmLkNnTz-sFvkwtdGnpw0GoRQi4YNOCszIEDRrNql67QmsqvGg0cgOdC-XOKvPNx_CG0kPpVU3sUUn67hFxHYwsQQ';
    $url = 'https://fcm.googleapis.com/fcm/send';
  
    $data = [
        "to" => $fcmToken,
        "notification" => [
            "title" => $title,
            "body" => $description,
        ],
        "data" => [
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "id" => "1", 
            "status"=> "done"
        ],
        "priority" => "high",
    ];
    $encodedData = json_encode($data);
    $headers = [
        'Authorization:key=' . $serverKey,
        'Content-Type: application/json',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
    // Execute post
    $result = curl_exec($ch);
    if ($result === false) {
        // die('Curl failed: ' . curl_error($ch));
    }
    // Close connection
    curl_close($ch);
    // FCM response
}
