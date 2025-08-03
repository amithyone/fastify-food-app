<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{

    public function get_balance(request $request)
    {

        $bal = User::where('api_key', $request->api_key)->first()->wallet ?? null;

        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "state can not be null"
            ], 422);

        }


        if ($bal != null && $request->action == "balance") {

            return response()->json([
                'status' => true,
                'main_balance' => $bal
            ], 200);

        }

        return response()->json([
            'status' => false,
            'message' => "Wrong or incorrect api key"
        ], 422);


    }

    public function get_world_countries(request $request)
    {
        return response()->json([
            'status' => true,
            'data' => get_world_countries()
        ], 200);


    }

    public function get_world_services(request $request)
    {
        return response()->json([
            'status' => true,
            'data' => get_world_services()
        ], 200);


    }


    public function check_availability(request $request)
    {


        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "state can not be null"
            ], 422);

        }


        if ($request->action == "check-availability") {

            $get_key = User::where('api_key', $request->api_key)->first() ?? null;

            if ($get_key == null) {

                return response()->json([
                    'status' => false,
                    'message' => "Wrong Api key"
                ], 422);

            }


            $key = env('WKEY');
            $curl = curl_init();

            $databody = array(
                "country" => $request->country,
                "service" => $request->service,
                'key' => $key,


            );


            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.smspool.net/sms/stock',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $databody,
            ));

            $var1 = curl_exec($curl);

            curl_close($curl);
            $var1 = json_decode($var1);

            $data['stock'] = $var1->amount ?? null;


            $databody = array(
                "key" => $key,
                "country" => $request->country,
                "service" => $request->service,
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
                    'Authorization: Bearer {{apikey}}'
                ),
            ));

            $var3 = curl_exec($curl);
            curl_close($curl);
            $var2 = json_decode($var3);

            $get_s_price = $var2->price ?? null;
            $high_price = $var2->high_price ?? null;
            $rate = $var2->success_rate ?? null;


            if ($high_price == null) {
                $price = $get_s_price * $get_key->api_percentage;
            } elseif ($high_price > 4) {
                $price = $high_price * $get_key->api_percentage ?? 1.3;
            } else {
                $price = $high_price * $get_key->api_percentage;
            }

            $get_rate = Setting::where('id', 1)->first()->rate;
            $margin = Setting::where('id', 1)->first()->margin;
            $ngnprice = ($price * $get_rate) + $margin;




            return response()->json([
                'status' => true,
                'cost' => $ngnprice,
                'stock' => $data['stock'],
                'country' => $request->country,
                'service' => $request->service,

            ], 200);


        } else {
            return response()->json([
                'status' => false,
                'message' => "action incorrect"
            ], 422);
        }
    }


    public function rent_world_number(request $request)
    {

        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "action can not be null"
            ], 422);

        }





        if ($request->action == "rent-world-number") {

            $key = env('WKEY');

            $get_key = User::where('api_key', $request->api_key)->first() ?? null;
            $country = $request->country;
            $service = $request->service;
            $id = $get_key->id;


            $databody = array(
                "key" => $key,
                "country" => $request->country,
                "service" => $request->service,
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
                    'Authorization: Bearer {{apikey}}'
                ),
            ));

            $var2 = curl_exec($curl);
            curl_close($curl);
            $var2 = json_decode($var2);

            $get_s_price = $var->price ?? null;
            $high_price = $var->high_price ?? null;
            $rate = $var->success_rate ?? null;

            if ($high_price == null) {
                $price = $get_s_price * $get_key->api_percentage;
            } elseif ($high_price > 4) {
                $price = $high_price * $get_key->api_percentage ?? 1.3;
            } else {
                $price = $high_price * $get_key->api_percentage;
            }

            $get_rate = Setting::where('id', 1)->first()->rate;
            $margin = Setting::where('id', 1)->first()->margin;
            $ngnprice = ($price * $get_rate) + $margin;

            if ($get_key->wallet < $ngnprice) {

                return response()->json([
                    'status' => false,
                    'message' => "INSUFFICIENT FUNDS, FUND YOUR WALLET",
                ], 422);

            }

            User::where('id', $get_key->id)->decrement('wallet', $ngnprice);


            $price = $ngnprice;
            $order = create_world_order($country, $service, $price, $id);


            if ($order == 5) {
                User::where('id', $get_key->id)->increment('wallet', $ngnprice);
                return response()->json([
                    'status' => false,
                    'message' => "NUMBER OUT OF STOCK",
                ], 422);
            }

            if ($order == 1) {
                User::where('id', $get_key->id)->increment('wallet', $ngnprice);
                $message = "FADDEDSMS | Low balance";
                send_notification($message);

                return response()->json([
                    'status' => false,
                    'message' => "ERROR OCCOURED",
                ], 422);
            }

            if ($order == 2) {
                User::where('id', $get_key->id)->increment('wallet', $ngnprice);
                $message = "FADDEDSMS | Error";
                send_notification($message);

                return response()->json([
                    'status' => false,
                    'message' => "ERROR OCCOURED",
                ], 422);
            }

            if ($order == 3) {

                $num = Verification::latest()->where('user_id', $get_key->id)->where('status', 1)->first()->phone;
                $id = Verification::latest()->where('user_id', $get_key->id)->where('status', 1)->first()->id;

                $data['num'] = $num;


                return response()->json([
                    'status' => true,
                    'order_id' => $id,
                    'phone_no' => $data['num'],
                    'country' => $request->country,
                    'service' => $request->service,

                ], 200);
            }


        }




    }


    public function get_world_sms(request $request)
    {

        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "action can not be null"
            ], 422);

        }





        if ($request->action == "get-world-sms") {

                $full_sms = Verification::where('id',$request->order_id)->first()->full_sms;
                $code = Verification::where('id',$request->order_id)->first()->sms;
            $country = Verification::where('id',$request->order_id)->first()->country;
            $service = Verification::where('id',$request->order_id)->first()->service;
            $status = Verification::where('id',$request->order_id)->first()->status;

            if($status == 1){
                $sms_status = "PENDING";
            }elseif ($status == 2){
                $sms_status = "COMPLETED";
            }else{
                $sms_status = "REJECTED";
            }



            return response()->json([
                    'status' => true,
                    'sms_status' => $sms_status,
                    'full_sms' => $full_sms,
                    'code' => $code,
                    'country' => $country,
                    'service' => $service,

                ], 200);
            }


        }





}
