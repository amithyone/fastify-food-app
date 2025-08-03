<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Setting;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorldNumberController extends Controller
{

    public function home(request $request)
    {

        $get_rate = Setting::where('id', 1)->first()->rate;
        $margin = Setting::where('id', 1)->first()->margin;

        $data['services'] = get_services();


        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;


        $wcountries = get_world_countries();
        $wservices = get_world_services();

        $verification = Verification::latest()->where('user_id', Auth::id())->get();



        $data['wservices'] = $wservices;
        $data['wcountries'] = $wcountries;
        $data['verification'] = $verification;


        $data['q_orderuk'] = ck_av("2", "1012") ?? 0;
        $data['ukamont'] = (world_price("2", "1012") ??  0 ) * $get_rate + $margin;




        $data['pend'] = 0;

        $data['product'] = null;

        $data['orders'] = Verification::latest()->where('user_id', Auth::id())->get();


        return view('world', $data);
    }



    public function check_av(Request $request)
    {





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

        $var = curl_exec($curl);

        curl_close($curl);
        $var = json_decode($var);

        $data['stock'] = $var->amount ?? null;





        $key = env('WKEY');


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

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);




        $get_s_price = $var->price ?? null;
        $high_price = $var->high_price ?? null;
        $rate = $var->success_rate ?? null;
        $product = 1;



        if($high_price == null){
            $price = $get_s_price;
        }elseif($high_price > 4){
            $price = $high_price * 1.3;
        }else{
            $price = $high_price;
        }






        if ($price == null) {
            return redirect('world')->with('error', 'Verification not available for selected service');
        } else {

            $get_rate = Setting::where('id', 1)->first()->rate;
            $margin = Setting::where('id', 1)->first()->margin;
            $verification = Verification::where('user_id', Auth::id())->get();
            $count_id = Country::where('country_id', $request->country)->first()->short_name ?? null;

            $ngnprice = ($price * $get_rate) + $margin;
            $data['get_rate'] = Setting::where('id', 1)->first()->rate;
            $data['margin'] = Setting::where('id', 1)->first()->margin;

            $data['q_orderuk'] = ck_av("1", "1012") ?? 0;
            $data['ukamont'] = (world_price("1", "1012") ??  0 ) * $get_rate + $margin;


            $data['count_id'] = $count_id;
            $data['serv'] = $request->service;
            $wcountries = get_world_countries();
            $wservices = get_world_services();
            $data['wservices'] = $wservices;
            $data['wcountries'] = $wcountries;
            $data['rate'] = $rate;
            $data['price'] = $ngnprice;
            $data['product'] = 1;
            $data['orders'] = Verification::latest()->where('user_id', Auth::id())->get();
            $data['services'] = get_services();





            $data['country'] =

            $data['number_order'] = null;

            $verifications = Verification::latest()->where('user_id', Auth::id())->where('status', 1)->get();
            if ($verifications->count() > 1) {
                $data['pend'] = 1;
            } else {
                $data['pend'] = 0;
            }

            $wservices = get_world_services();
            $verification = Verification::where('user_id', Auth::id())->get();
            $data['wservices'] = $wservices;

            $data['verification'] = Verification::latest()->where('user_id', Auth::id())->paginate('10');


            return view('world', $data);
        }
    }



    public function  get_smscode(request $request)
    {


        $sms =  Verification::where('phone', $request->num)->first()->sms ?? null;
        $originalString = 'waiting for sms';
        $processedString = str_replace('"', '', $originalString);


        if ($sms == null) {
            return response()->json([
                'message' => $processedString
            ]);
        } else {

            return response()->json([
                'message' => $sms
            ]);
        }
    }


    public function webhook(request $request)
    {

        $message = json_encode($request->all());
        send_notification($message);
    }








    public function order_now(Request $request)
    {
        if (Auth::user()->disabled == 1) {
            return back()->with('error', 'Error processing transaction');
        }

        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }


        if($request->price < 0 || $request->price == 0){
            return back()->with('error', "something went wrong");
        }

        if($request->price < 600 ){
            return back()->with('error', "something went wrong");
        }


        $country = $request->country;
        $service = $request->service;
        $price = $request->price;
        $id = Auth::id();




        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;


        $gcost = pool_cost($service, $country);

        $cost = ($data['get_rate'] * $gcost) + $data['margin'];


        if (Auth::user()->wallet < $cost) {
            return back()->with('error', "Insufficient Funds");
        }


        if (Auth::user()->wallet < $cost) {
            return back()->with('error', "Insufficient Funds");
        }



        $order = create_world_order($country, $service, $price, $id, $cost);

        if ($order == 5) {
            return redirect('world')->with('error', 'Number Currently out of stock, Please check back later');
        }

        if ($order == 1) {
            $message = "FADDEDSMS | Low balance";
            send_notification($message);
            return redirect('world')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 2) {
            $message = "FADDEDSMS | Error";
            send_notification($message);
            return redirect('world')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 3) {
           return redirect('orders')->with('message', "Order successful");
        }
    }


    public function cancleworld_sms(Request $request)
    {
        $order = Verification::where('id', $request->id)->first() ?? null;
        if ($order == null) {
            return redirect('us')->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return redirect('us')->with('message', "Order Completed");
        }


        $orderID = $order->order_id;
        $can_order = cancel_world_order($orderID);
            if ($order->status == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('world')->with('message', "Order has been canceled, NGN$amount has been refunded");
            }

        if ($can_order == 0) {
            return back()->with('message', "Your order cannot be cancelled yet, please try again later.");
        }


        if ($can_order == 1) {
            $amount = number_format($order->cost, 2);
            User::where('id', Auth::id())->increment('wallet', $order->cost);
            Verification::where('id', $request->id)->delete();
            return redirect('world')->with('message', "Order has been canceled, NGN$amount has been refunded");
        }


        if ($can_order == 3) {
            $order = Verification::where('id', $request->id)->first() ?? null;
            if ($order->status != 1 || $order == null) {
                return redirect('world')->with('error', "Please try again later");
            }
            $amount = number_format($order->cost, 2);
            User::where('id', Auth::id())->increment('wallet', $order->cost);
            Verification::where('id', $request->id)->delete();
            return redirect('world')->with('message', "Order has been canceled, NGN$amount has been refunded");
        }
    }


    public function check_sms(Request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;

        if ($order == null) {
            return back()->with('error', 'Order not found');
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $sms = check_sms($orderID);

            if ($sms == 1) {
                return redirect('home2')->with('error', 'Sms Pending, please wait and refresh again');
            }

            if ($sms == 6) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been canceled, NGN$amount has been refunded");
            }

            if ($sms == 6) {
                return back()->with('message', 'Sms Received, order completed');
            }
        }
    }

}
