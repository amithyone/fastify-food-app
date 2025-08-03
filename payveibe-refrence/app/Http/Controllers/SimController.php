<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Setting;
use App\Models\User;
use App\Models\Verification;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimController extends Controller
{

    public function index(request $request)
    {

        $countries = get_s_countries();


        $verification = Verification::where('user_id', Auth::id())->get();
        $s_rate = Setting::where('id', 3)->first();

        //$data['services'] = $services;
        $data['countries'] = $countries;
        $data['services'] = get_services();
        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['orders'] = Verification::where('user_id', Auth::id())->paginate('10');

        $data['verification'] = $verification;

        $data['product'] = null;

        $data['rate'] = $s_rate->rate;
        $data['margin']= $s_rate->margin;



        return view('simworld', $data);

    }
    public function order_csms(request $request)
    {






        $token = env('SIMTOKEN');
        $request->validate([
            'country' => 'required|string',
            'operator' => 'required|string',
            'product' => 'required|string',
            'count' => 'required|string',

        ]);



        if($request->input('count') == "0"){
            return 2;
        }




        $country = $request->input('country');
        $operator = $request->input('operator');
        $product = $request->input('product');
        $cost = get_s_product_cost($operator, $country, $product);

        if($cost == 0){
            return 0;
        }

        if(Auth::user()->wallet < $cost){
            return 9;
        }

        $client = new Client();

        try {
            $response = $client->get("https://5sim.net/v1/user/buy/activation/$country/$operator/$product", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);




            $responseBody = json_decode($response->getBody(), true);

            if($responseBody['price'] < 0 || $request->price == 0){
                return back()->with('error', "something went wrong");
            }

            if($responseBody['price'] < 600 ){
                return back()->with('error', "something went wrong");
            }

            $phone = str_replace("+", "", $responseBody['phone']);

            User::where('id', Auth::id())->decrement('wallet', $cost);

            Verification::where('phone', $phone)->where('status', 2)->delete() ?? null;
            $ver = new Verification();
            $ver->user_id = Auth::id();
            $ver->phone = $phone;
            $ver->order_id = $responseBody['id'];
            $ver->country =$responseBody['country'];
            $ver->service = $responseBody['product'];
            $ver->cost = $cost;
            $ver->api_cost = $responseBody['price'];
            $ver->status = 1;
            $ver->type = 3;
            $ver->save();


            $data['id'] = $responseBody['id'];
            $data['code'] = 200;
            return $data;


        } catch (\Exception $e) {
            // Handle errors
            return response()->json([
                'message' => 'Failed to complete purchase',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function get_s_country(request $request)
    {

        $balance = get_s_countries();

        dd($balance);

    }
    public function check_av(Request $request)
    {

        $token = env('SIMTOKEN');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://5sim.net/v1/guest/prices?country=$request->country&product=$request->product",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => " ",
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);




        $get_s_price = $var->price ?? null;
        $high_price = $var->high_price ?? null;
        $rate = $var->success_rate ?? null;
        $product = 1;

        if($high_price > 4){
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


            $data['count_id'] = $count_id;
            $data['serv'] = $request->service;
            $data['verification'] = $verification;
            $countries = get_world_countries();
            $services = get_world_services();
            $data['services'] = $services;
            $data['countries'] = $countries;
            $data['rate'] = $rate;
            $data['price'] = $ngnprice;
            $data['product'] = 1;

            $data['number_order'] = null;

            $verifications = Verification::where('user_id', Auth::id())->where('status', 1)->get();
            if ($verifications->count() > 1) {
                $data['pend'] = 1;
            } else {
                $data['pend'] = 0;
            }


            return view('world', $data);
        }
    }


    public function delete_sms(request $request)
    {

        $token = env('SIMTOKEN');
        $ch = curl_init();
        $id = Verification::where('id', $request->id)->first()->order_id ?? null;
        $cost = Verification::where('id', $request->id)->first()->cost ?? null;
        $user_id = Verification::where('id', $request->id)->first()->user_id ?? null;

        if($id == null){
            return back()->with('error', "Verification has been deleted");
        }

        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/user/cancel/' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $token;
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        $var = json_decode($result);
        $status = $var->status ?? null;



        if($status == "CANCELED"){

            Verification::where('id', $request->id)->delete();
            User::where('id', $user_id)->increment('wallet', $cost);
            return back()->with('message', "Number Canceled, NGN $cost has been refunded");

        }

        $status = Verification::where('id', $request->id)->first()->status ?? null;

        if($status != null && $status != 2){
            Verification::where('id', $request->id)->delete();
            User::where('id', $user_id)->increment('wallet', $cost);
            return back()->with('message', "Number Canceled, NGN $cost has been refunded");
        }




    }
    public function admin_delete_sms(request $request)
    {

        $token = env('SIMTOKEN');
        $ch = curl_init();
        $id = Verification::where('id', $request->id)->first()->order_id;
        $cost = Verification::where('id', $request->id)->first()->cost;
        $user_id = Verification::where('id', $request->id)->first()->user_id;

        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/user/cancel/' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $token;
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        $var = json_decode($result);
        $status = $var->status ?? null;



        if($status == "CANCELED"){

            Verification::where('id', $request->id)->delete();
            User::where('id', $request->user_id)->increment('wallet', $cost);
            return back()->with('message', "Number Canceled, NGN $cost has been refunded");

        }
        Verification::where('id', $request->id)->delete();
        return back()->with('error', "Number Canceled");

    }


    public function get_c_sms(request $request){

        $token = env('SIMTOKEN');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/user/check/' . $request->id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $token;
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        $var = json_decode($result);
        $status = $var->status ?? null;
        $sms = $var->sms[0]->text ?? null;



        if($status == 'RECEIVED'){

            if($sms == null){
                $originalString = 'sms loading...';
                $processedString = str_replace('"', '', $originalString);
                return response()->json([
                    'message' => $processedString
                ]);
            }else{
                Verification::where('order_id', $request->id)->update(['full_sms' => $var->sms[0]->text, 'sms' => $var->sms[0]->code, 'status' => 2]);
            }

        }


        if($status == "FINISHED"){
            Verification::where('order_id', $request->id)->update(['full_sms' => $var->sms[0]->text, 'sms' => $var->sms[0]->code, 'status' => 2]);
        }

        $originalString = 'sms loading...';
        $processedString = str_replace('"', '', $originalString);
        return response()->json([
            'message' => $processedString
        ]);

    }

}
