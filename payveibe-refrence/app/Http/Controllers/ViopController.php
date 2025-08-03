<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\Setting;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViopController extends Controller
{


    public function get_viop_services()
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


    public function searchservices(request $request)
    {
        $query = $request->query('q');
        $services = $this->get_viop_services();
        if ($query && $services) {
            $services = array_filter($services, function ($service) use ($query) {
                return stripos($service->name, $query) !== false; // Case-insensitive search
            });
        }
        return response()->json(array_values($services));
    }

    public function index()
    {


        $data['servicesd'] = get_services();
        $data['get_rated'] = Setting::where('id', 1)->first()->rate;
        $data['margind'] = Setting::where('id', 1)->first()->margin;
        $s_rate = Setting::where('id', 4)->first();
        $data['services'] = get_viop_services();
        $data['get_rate'] = Setting::where('id', 4)->first()->rate;
        $data['margin'] = Setting::where('id', 4)->first()->margin;
        $data['verification'] = Verification::where('user_id', Auth::id())->paginate('10');
        $data['product'] = null;
        $data['rate'] = $s_rate->rate;
        $data['margin'] = $s_rate->margin;


        return view('server3', $data);

    }


    public function update_viop_cost(request $request)
    {
        Setting::where('id', 4)->update(['margin' => $request->cost]);

        return back()->with('message', "Cost Update Successfully");

    }

    public function update_viop_rate(request $request)
    {
        Setting::where('id', 4)->update(['rate' => $request->rate]);

        return back()->with('message', "Rate Update Successfully");

    }


    public function viop_buy(request $request)
    {


        $product_id = $request->product_id;
        $get_cost = get_viop_cost($product_id);


        $price2 = $get_cost['price'];
        $av = $get_cost['av'];
        $cost = $get_cost['price'];



        if($request->price < 0 || $request->price == 0){
            return response()->json([
                'message' => 'Not Available'
            ]);
        }

        if($request->price < 600 ){
            return response()->json([
                'message' => 'Not Available'
            ]);
        }

        if ($av == 0) {
            return response()->json([
                'message' => 'Not Availabe'
            ]);
        }


        $s_rate = Setting::where('id', 4)->first();
        $price = ($price2 * $s_rate->rate) + $s_rate->margin;

        if (Auth::user()->wallet < $price) {
            return response()->json([
                'message' => 'Insufficient funds'
            ]);
        }

        $buyviop = buy_viop($product_id, $price, $cost);

        if($buyviop == 1){
            return response()->json([
                'message' => 'Verification successful'
            ]);
        }else{
            return response()->json([
                'message' => 'Not available'
            ]);
        }


    }


    public function get_viopsms(request $request)
    {

        $sms = Verification::where('phone', $request->num)->first()->sms ?? null;

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


    public
    function viop_webhook(request $request)
    {

        $activationId = $request->message->order_id;
        $code = $request->message->sms;
        $orders = Verification::where('order_id', $activationId)->update(['sms' => $code, 'status' => 2]);
        $message = json_encode($request->all());
        send_notification($message);


    }


    public function cancle_viop(request $request)
    {

        $product_id = $request->id;
        $service = Verification::where('order_id', $product_id)->first()->service ?? null;
        if($service == null){
            return back()->with('error', 'Verification not found');
        }
        $c_viop = cancle_viop($product_id, $service);
        $user_id = Verification::where('order_id', $product_id)->first()->user_id ?? null;
        $cost = Verification::where('order_id', $product_id)->first()->cost ?? null;


        if($c_viop == 1){
            User::where('id', $user_id)->increment('wallet', $cost);
            Verification::where('order_id', $product_id)->delete();
            return back()->with('message', "Order canceled, You have been funded NGN $cost");

        }else{
            User::where('id', $user_id)->increment('wallet', $cost);
            Verification::where('order_id', $product_id)->delete();
            return back()->with('message', "Order canceled, You have been funded NGN $cost");

        }





    }



}
