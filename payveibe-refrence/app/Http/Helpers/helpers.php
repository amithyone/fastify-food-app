<?php

use App\Constants\Status;
use App\Models\Extension;
use App\Models\Setting;
use App\Models\User;
use App\Models\Verification;
use App\Lib\GoogleAuthenticator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


function resolve_complete($order_id)
{

    $curl = curl_init();

    $databody = array('order_id' => "$order_id");

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.sprintpay.online/api/resolve-complete',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);


    $status = $var->status ?? null;
    if ($status == true) {
        return 200;
    } else {
        return 500;
    }
}



function send_notification($message)
{

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.telegram.org/bot7515872256:AAHDrG_LeWM23KVDJ9YF2WiKRCDmebgca0o/sendMessage?chat_id=7174457646',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'chat_id' => "7174457646",
                'text' => $message,
            ),
            CURLOPT_HTTPHEADER => array(),
        ));

        $var = curl_exec($curl);
        curl_close($curl);

        $var = json_decode($var);
}


//    function send_notification2($message)
//    {
//
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//             CURLOPT_URL => 'https://api.telegram.org/bot7729529581:AAEouUbQN7G2sVSQnH4D77k8H1TjT8t4Djw/sendMessage?chat_id=986615350',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => array(
//                'chat_id' => "7174457646",
//                'text' => $message,
//
//            ),
//            CURLOPT_HTTPHEADER => array(),
//        ));
//
//        $var = curl_exec($curl);
//        curl_close($curl);
//
//        $var = json_decode($var);
//    }



function session_resolve($session_id, $ref){

    $curl = curl_init();

    $databody = array(
        'session_id' => "$session_id",
        'ref' => "$ref"
    );


    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.sprintpay.online/api/resolve',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    $message = $var->message ?? null;
    $status = $var->status ?? null;

    $amount = $var->amount ?? null;

    return array([
        'status' => $status,
        'amount' => $amount,
        'message' => $message
    ]);


}




function get_services(){

    $APIKEY = env('KEY');

    $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getPricesVerification",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $services = $var ?? null;

        if ($var == null) {
            $services = null;
        }

        return $services;

}


function create_order($service, $price, $cost, $service_name){



   $APIKEY = env('KEY');
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getNumber&service=$service&max_price=$cost",
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
   ));

   $var = curl_exec($curl);
   curl_close($curl);
   $result = $var ??  null;

    if(strstr($result, "ACCESS_NUMBER") !== false) {

        $parts = explode(":", $result);
        $accessNumber = $parts[0];
        $id = $parts[1];
        $phone = $parts[2];


        Verification::where('phone', $phone)->where('status', 2)->delete() ?? null;

        $ver = new Verification();
        $ver->user_id = Auth::id();
        $ver->phone = $phone;
        $ver->order_id = $id;
        $ver->country = "US";
        $ver->service = $service_name;
        $ver->cost = $price;
        $ver->api_cost = $cost;
        $ver->status = 1;
        $ver->type = 1;
        $ver->save();

        User::where('id', Auth::id())->decrement('wallet', $price);

        return 1;

    }elseif($result == "MAX_PRICE_EXCEEDED" || $result == "NO_NUMBERS" || $result == "TOO_MANY_ACTIVE_RENTALS" || $result == "NO_MONEY") {
        return 0;
    }else{
        return 0;
    }




}

function cancel_order($orderID){


   $APIKEY = env('KEY');
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=setStatus&id=$orderID&status=8",
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
   ));

    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ?? null;

    if(strstr($result, "ACCESS_CANCEL") !== false){

        return 1;

    }else{

        return 0;

    }




}

function check_sms($orderID){



   $APIKEY = env('KEY');
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getStatus&id=$orderID",
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
   ));

    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ?? null;


    if(strstr($result, "NO_ACTIVATION") !== false){

        return 1;

    }

    if(strstr($result, "NO_ACTIVATION") !== false){

        return 1;

    }

    if(strstr($result, "STATUS_WAIT_CODE") !== false){

        return 2;

    }

    if(strstr($result, "STATUS_CANCEL") !== false){

        return 4;

    }



    if(strstr($result, "STATUS_OK") !== false) {


    $parts = explode(":", $result);
    $text = $parts[0];
    $sms = $parts[1];

        $data['sms'] = $sms;
        $data['full_sms'] = $sms;

        Verification::where('order_id', $orderID)->update([
            'status' => 2,
            'sms' => $sms,
            'full_sms' => $sms,
        ]);

        return 3;

    }


}




function get_world_countries(){

    $key = env('WKEY');

    $countries = Cache::remember('smspool_countries', 3600, function () use ($key) {
        Log::info('Requesting countriees from SMS Pool API', ['key' => $key]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(50)
            ->post("https://api.smspool.net/country/retrieve_all", [
                'key' => $key,
            ]);

        Log::info('Response received from SMS Pool API', ['response' => $response->body()]);

        if ($response->successful()) {
            return $response->json() ?? null;
        }

        Log::error('API call failed', ['response' => $response->body()]);
        return null;
    });




    return $countries;




}


function get_world_services(){


    $key = env('WKEY');

    $services = Cache::remember('smspool_services', 3600, function () use ($key) {
        Log::info('Requesting services from SMS Pool API', ['key' => $key]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(50)
            ->post("https://api.smspool.net/service/retrieve_all", [
                'key' => $key,
            ]);

        Log::info('Response received from SMS Pool API', ['response' => $response->body()]);

        if ($response->successful()) {
            return $response->json() ?? null;
        }

        Log::error('API call failed', ['response' => $response->body()]);
        return null;
    });



    return $services;




}


function create_world_order($country, $service, $price, $id, $cost){


    $key = env('WKEY');
    $curl = curl_init();

    $databody = array(
        'country' => $country,
        'service' => $service,
        'key' => $key,


    );
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.smspool.net/purchase/sms',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);
    $success = $var->success ?? null;

    if($success == 0){
        return 5;

    }

    if($success == 1){

        Verification::where('phone', $var->cc.$var->phonenumber)->where('status', 2)->delete() ?? null;

        $ver = new Verification();
        $ver->user_id = Auth::id() ?? $id;
        $ver->phone = $var->cc.$var->phonenumber;
        $ver->order_id = $var->order_id;
        $ver->country = $var->country;
        $ver->service = $var->service;
        $ver->expires_in = $var->expires_in / 10 - 20;
        $ver->cost = $cost;
        $ver->api_cost = $var->cost;
        $ver->status = 1;
        $ver->type = 2;

        $ver->save();


        User::where('id', Auth::id())->decrement('wallet', $cost);

        return 3;



    }





    $status = $var->type ?? null;

    if($status == "BALANCE_ERROR"){
        return  1;
    }

    if($status == null){
        return 2;

    }




}

function cancel_world_order($orderID){

    $key = env('WKEY');
    $curl = curl_init();

    $databody = array(
        'orderid' => $orderID,
        'key' => $key,
    );
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.smspool.net/sms/cancel',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);




    $status = $var->success ?? null;
    $message = $var->message ?? null;

    if($status == 0 && $message == "We could not find this order!"){
        return 3;
    }

    if($status == 0 && $message == "Your order cannot be cancelled yet, please try again later."){
        return 0;
    }


    if($status == 0){
        return 0;
    }

    if($status == 1){
        return 1;
    }




}

function check_world_sms($orderID){

    $key = env('KEY');
    $curl = curl_init();

    $databody = array(
        'orderid' => $orderID,
        'key' => $key,
    );
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.smspool.net/sms/check',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    $status = $var->status ?? null;
    $sms = $var->sms ?? null;
    $full_sms = $var->full_sms ?? null;


    if($status == 1){

        Verification::where('order_id', $orderID)->update([
            'expires_in' => $var->time_left / 10 - 20,
        ]);

        return 1;
    }

    if($status == 6){
        return 6;
    }


    if($status == 3){

        $data['sms'] = $sms;
        $data['full_sms'] = $full_sms;

        Verification::where('order_id', $orderID)->update([
            'status' => 2,
            'sms' => $sms,
            'full_sms' => $full_sms,
        ]);

        return 3;
    }



    dd($var);
}




function ck_av($country, $service)
{

    $key = env('WKEY');
    $country = $country;
    $service = $service;

    $amount = Cache::remember('smspool_whatsapp_service_stock', 2880, function () use ($key, $country, $service) {
        Log::info('Requesting service stock from SMS Pool API', ['key' => $key]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->timeout(50)
            ->asForm() // Use asForm for multipart/form-data
            ->post("https://api.smspool.net/sms/stock", [
                'service' => $service,
                'country' => $country,
                'key' => $key,
            ]);

        Log::info('Response received from SMS Pool API', ['response' => $response->body()]);

        if ($response->successful()) {
            return $response->json() ?? null;
        }

        Log::error('API call failed', [
            'response' => $response->body(),
            'status' => $response->status(),
            'reason' => $response->reason(),
        ]);
        return null;
    });

    return $amount['amount'];

}

function world_price($country, $service)
{


    $key = env('WKEY');
    $country = $country;
    $service = $service;

    $var = Cache::remember('smspool_whatsapp_service_price', 2880, function () use ($key, $country, $service) {
        Log::info('Requesting service price from SMS Pool API', ['key' => $key]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->timeout(50)
            ->asForm() // Use asForm for multipart/form-data
            ->post("https://api.smspool.net/request/price", [
                'service' => $service,
                'country' => $country,
                'key' => $key,
            ]);

        Log::info('Response received for price from SMS Pool API', ['response' => $response->body()]);

        if ($response->successful()) {
            return $response->json() ?? null;
        }

        Log::error('API call failed', [
            'response' => $response->body(),
            'status' => $response->status(),
            'reason' => $response->reason(),
        ]);
        return null;
    });

    $get_s_price = $var['price'] ?? null;
    $high_price = $var['high_price'] ?? null;

    if($high_price == null){
        $price = $get_s_price;
    }elseif($high_price > 4){
        $price = $high_price * 1.3;
    }else{
        $price = $high_price;
    }

    return $price;

}


function pool_cost($service, $country){

    $key = env('WKEY');

    $databody = array(
        "key" => $key,
        "country" => $country,
        "service" => $service,
        "pool" => '',
    );

    $body = json_encode($databody);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.smspool.net/request/price',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $key"
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);


    $get_s_price = $var->price ?? null;
    $high_price = $var->high_price ?? null;
    $rate = $var->success_rate ?? null;

    if($high_price == null){
        $price = $get_s_price;
    }elseif($high_price > 4){
        $price = $high_price * 1.3;
    }else{
        $price = $high_price;
    }


    return $price;


}

function get_d_price($service){
    $APIKEY = env('KEY');
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getPrices&service=$service",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);


    foreach($var as $key => $value){

        $service2['data'] =  $value;

    }

    $result = $service2["data"]->$service->cost;
    return $result;

}


function get_s_countries()
{
    $token = env('SIMTOKEN');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/guest/countries');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array();
    $headers[] = 'Authorization: Bearer ' . $token;
    $headers[] = 'Accept: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $var = curl_exec($ch);
    curl_close($ch);
    $inputArray = json_decode($var, true);

    $result = [];
    foreach ($inputArray as $key => $value) {
        $result[$key] = $value['text_en'];
    }

    return $result;


}

function get_s_product_cost($operator, $country, $product)
{
    $token = env('SIMTOKEN');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/guest/products/' . $country . '/' . $operator);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array();
    $headers[] = 'Authorization: Bearer ' . $token;
    $headers[] = 'Accept: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $var = curl_exec($ch);
    curl_close($ch);
    $var = json_decode($var);

    $filterKeyword = $product ?? '';

    if (is_object($var)) {
        $data = json_decode(json_encode($var), true);
    }

    $filteredData = array_filter($data, function($key) use ($filterKeyword) {
        return stripos($key, $filterKeyword) !== false;
    }, ARRAY_FILTER_USE_KEY);

    if($filteredData == []){
        return 0;
    }


    $prices = [];
    foreach ($filteredData as $item) {
        if (isset($item['Price'])) {
            $prices[] = $item['Price'];
        }
    }


    $s_rate = Setting::where('id', 3)->first();
    $data['rate'] = $s_rate->rate;
    $data['margin']= $s_rate->margin;

    $fcost = $data['rate'] * $prices[0] + $data['margin'];

    return $fcost;


}


function get_viop_services()
{

    $vemail = env('VEMAIL');
    $vpass = env('VPASS');


    $databody = array(
        "email" => $vemail,
        "password" => $vpass,
        "type" => "short_term",
        "network" => "",
        "id" => "",
    );

    $body = json_encode($databody);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://nonvoipusnumber.com/manager/api/products',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);
    $services = $var->message ?? null;

    if ($var == null) {
        $services = null;
    }

    return $services;


}


function get_viop_cost($product_id)
{
    $vemail = env('VEMAIL');
    $vpass = env('VPASS');

    $databody = array(
        "email" => $vemail,
        "password" => $vpass,
        "type" => "short_term",
        "network" => "",
        "id" => $product_id,
    );

    $body = json_encode($databody);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://nonvoipusnumber.com/manager/api/products',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);
    $price = $var->message[0]->price ?? null;
    $av = $var->message[0]->available ?? null;


    $data['price'] = $price;
    $data['av'] = $av;
    return $data;



}



function buy_viop($product_id, $price, $cost)
{


    $vemail = env('VEMAIL');
    $vpass = env('VPASS');

    $databody = array(
        "email" => $vemail,
        "password" => $vpass,
        "type" => "short_term",
        "network" => "",
        "product_id" => $product_id,
    );

    $body = json_encode($databody);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://nonvoipusnumber.com/manager/api/order',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);
    $status = $var->status ??  null;

    if($status == "success"){

        $phone = $var->message[0]->number;
        $service_name = $var->message[0]->service;
        $id = $var->message[0]->order_id;

        Verification::where('phone', $phone)->where('status', 2)->delete() ?? null;
        $ver = new Verification();
        $ver->user_id = Auth::id();
        $ver->phone = $phone;
        $ver->order_id = $id;
        $ver->country = "US";
        $ver->service = $service_name;
        $ver->cost = $price;
        $ver->api_cost = $cost;
        $ver->status = 1;
        $ver->type = 4;
        $ver->save();

        User::where('id', Auth::id())->decrement('wallet', $price);

        return 1;
    }else{

        return 2;
    }



}


function cancle_viop($product_id, $service)
{


    $vemail = env('VEMAIL');
    $vpass = env('VPASS');

    $databody = array(
        "email" => $vemail,
        "password" => $vpass,
        "service" => $service,
        "order_id" => $product_id,
    );

    $body = json_encode($databody);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://nonvoipusnumber.com/manager/api/reject',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);
    $status = $var->status ??  null;
    if($status == "success"){
        return 1;
    }else{
        return 2;
    }



}



function text_verified_auth()
{

    $text_username = env('TEXTUSERNAME');
    $text_api = env('TEXTAPI');

    $databody = array(

    );

    $body = json_encode($databody);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.textverified.com/api/pub/v2/auth',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "X-API-USERNAME: $text_username",
            "X-API-KEY: $text_api"
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    if($var != null){
        return $var->token;
    }







}


function text_services()
{


    $databody = array(

    );

    $body = json_encode($databody);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.textverified.com/api/pub/v2/services?numberType=viop&reservationType=verification',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
        ),
    ));

    $var = curl_exec($curl);
    dd($var);
    curl_close($curl);
    $var = json_decode($var);



    if($var != null){
        return $var->token;
    }


}

function tiger_sms_auth() {
    $APIKEY = env('TIGER_SMS_API_KEY');
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://tigersms.com/api/v1/auth",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'api_key' => $APIKEY
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}

function get_tiger_services() {
    $auth = tiger_sms_auth();
    if (!$auth || !isset($auth->token)) {
        return null;
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://tigersms.com/api/v1/services",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $auth->token,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}

function create_tiger_order($service, $price, $cost) {
    $auth = tiger_sms_auth();
    if (!$auth || !isset($auth->token)) {
        return 0;
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://tigersms.com/api/v1/orders",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(array(
            'service' => $service,
            'max_price' => $cost
        )),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $auth->token,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($response);

    if (!$result || !isset($result->order_id)) {
        return 0;
    }

    $user = Auth::user();
    $user->wallet -= $price;
    $user->save();

    $verification = new Verification();
    $verification->user_id = Auth::id();
    $verification->order_id = $result->order_id;
    $verification->phone = $result->phone;
    $verification->service = $service;
    $verification->price = $price;
    $verification->cost = $cost;
    $verification->status = 1;
    $verification->type = 3; // Type 3 for Tiger SMS
    $verification->expires_in = 300; // 5 minutes
    $verification->save();

    return 1;
}

function cancel_tiger_order($order_id) {
    $auth = tiger_sms_auth();
    if (!$auth || !isset($auth->token)) {
        return false;
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://tigersms.com/api/v1/orders/" . $order_id . "/cancel",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $auth->token,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($response);

    return $result && isset($result->status) && $result->status === 'success';
}

function check_tiger_sms($order_id) {
    $auth = tiger_sms_auth();
    if (!$auth || !isset($auth->token)) {
        return null;
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://tigersms.com/api/v1/orders/" . $order_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $auth->token,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($response);

    if (!$result) {
        return null;
    }

    // Update verification status based on order status
    $verification = Verification::where('order_id', $order_id)->first();
    if ($verification) {
        if ($result->status === 'success' && isset($result->sms)) {
            $verification->sms = $result->sms;
            $verification->status = 2; // Success
            $verification->save();
        } elseif ($result->status === 'failed') {
            $verification->status = 3; // Failed
            $verification->save();
        }
    }

    return $result;
}



