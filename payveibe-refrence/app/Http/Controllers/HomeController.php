<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;
use App\Models\ManualPayment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\SoldLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public function index(request $request)
    {
        $data['services'] = get_services();
        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['verification'] = Verification::where('user_id', Auth::id())->paginate('10');
        $data['order'] = 0;
        return view('welcome', $data);
    }


    public function home(request $request)
    {

        $data['services'] = get_services();
        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->paginate(20);
        $data['order'] = 0;
        $verification = Verification::where('user_id', Auth::id())->get();
        $data['pend'] = 0;
        $data['product'] = null;
        $data['orders'] = Verification::where('user_id', Auth::id())->get();

        return view('home', $data);
    }


    public function pendng_sms(Request $request)
    {

        return view('receive-sms');

    }


    public function updatesec(request $request)
    {

        $ver = Verification::where('id', $request->id)->first()->status;

        if ($ver == 1) {
            $secs = Verification::where('id', $request->id)->update(['expires_in' => $request->secs]);
        }

    }


    public function getInitialCountdown(request $request)
    {

        $ver = Verification::where('id', $request->id)->first()->status;
        if ($ver == 1) {
            $secs = Verification::where('id', $request->id)->first()->expires_in;
            return response()->json([
                'seconds' => $secs
            ]);
        }


    }


    public function server1_order(Request $request)
    {

        if ($request->price < 0 || $request->price == 0) {
            return back()->with('error', "something went wrong");
        }

        if ($request->price < 600) {
            return back()->with('error', "something went wrong");
        }


        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }

        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;


        $service = $request->service;

        $gcost = get_d_price($service);

        $costs = ($data['get_rate'] * $gcost) + $data['margin'];
        if (Auth::user()->wallet < $costs) {
            return back()->with('error', "Insufficient Funds");
        }


        $service = $request->service;
        $price = $request->price;
        $cost = $request->cost;
        $service_name = $request->name;

        $order = create_order($service, $price, $cost, $service_name, $costs);
        if ($order == 8) {
            return back()->with('error', "Insufficient Funds");
        }

        if ($order == 8) {
            return back()->with('error', "Insufficient Funds");
        }

        if ($order == 8) {
            return back()->with('error', "Insufficient Funds");
        }


        //dd($order);

        if ($order == 9) {

            $ver = Verification::where('status', 1)->first() ?? null;
            if ($ver != null) {
                return redirect('us');
            }
            return redirect('us');
        }

        if ($order == 0) {
            return redirect('home')->with('error', 'Number Currently out of stock, Please check back later');
        }


        if ($order == 1) {
            return redirect('orders');
        }
    }


    public function receive_sms(Request $request)
    {

        $type = Verification::where('user_id', Auth::id())->where('id', $request->phone)->first()->type;
        if ($type == 2) {
            $data['sms_order'] = Verification::where('user_id', Auth::id())->where('id', $request->phone)->first();
            $data['order'] = 1;

            $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

            return view('receivesmsworld', $data);

        }
        $data['sms_order'] = Verification::where('user_id', Auth::id())->where('id', $request->phone)->first();
        $data['order'] = 1;

        $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

        return view('receivesms', $data);

    }


    public function admin_cancle_sms(Request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;
        $user_id = $order->user_id;


        if ($order == null) {
            return back()->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return back()->with('message', "Order Completed");
        }

        if ($order->status == 1 && $order->type == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_order($orderID);

            if ($request->delete == 1) {

                if ($order->status == 1) {

                    $amount = number_format($order->cost, 2);
                    Verification::where('id', $request->id)->delete();
                    User::where('id', $user_id)->increment('wallet', $order->cost);
                    $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                    send_notification($message);
                    return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
                }


            }


            if ($can_order == 0) {
                return back()->with('error', "Order has been removed");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                User::where('id', $user_id)->increment('wallet', $order->cost);

                $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                send_notification($message);

                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;

                if ($order->status != 1 || $order == null) {
                    return back()->with('error', "Please try again later");
                }
                Verification::where('id', $request->id)->delete();
                $amount = number_format($order->cost, 2);
                User::where('id', $user_id)->increment('wallet', $order->cost);

                $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                send_notification($message);


                return redirect()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }
        }

        if ($order->status == 1 && $order->type == 2) {


            $orderID = $order->order_id;

            $can_order = cancel_world_order($orderID);

            if ($request->delete == 1) {


                if ($order->status == 1) {

                    $amount = number_format($order->cost, 2);
                    Verification::where('id', $request->id)->delete();

                    User::where('id', $user_id)->increment('wallet', $order->cost);

                    $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                    send_notification($message);


                    return back()->with('message', "Order has been canceled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {
                return back()->with('message', "Your order cannot be cancelled yet, please try again later.");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                User::where('id', $user_id)->increment('wallet', $order->cost);
                $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                send_notification($message);


                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return back()->with('error', "Please try again later");
                }
                Verification::where('id', $request->id)->delete();

                $amount = number_format($order->cost, 2);
                User::where('id', $user_id)->increment('wallet', $order->cost);

                $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                send_notification($message);

                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }
        }
    }




    public function fund_wallet(Request $request)
    {
        $user = Auth::id() ?? null;
        $pay = PaymentMethod::all();
        $status = AccountDetail::where('id', 1)->first()->status;
        $transaction = Transaction::query()
            ->orderByRaw('updated_at DESC')
            ->where('user_id', Auth::id())
            ->paginate(10);

        return view('fund-wallet', compact('user', 'pay', 'status', 'transaction'));
    }


    public function fund_now(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|gt:0',
        ]);


        Transaction::where('user_id', Auth::id())->where('status', 1)->delete() ?? null;


        if ($request->type == 1) {

            if ($request->amount < 1000) {
                return back()->with('error', 'You can not fund less than NGN 1000');
            }


            if ($request->amount > 100000) {
                return back()->with('error', 'You can not fund more than NGN 100,000');
            }


            $key = env('WEBKEY');
            $ref = "VERF" . random_int(000, 999) . date('ymdhis');
            $email = Auth::user()->email;

            $url = "https://web.sprintpay.online/pay?amount=$request->amount&key=$key&ref=$ref&email=$email";


            $data = new Transaction();
            $data->user_id = Auth::id();
            $data->amount = $request->amount;
            $data->ref_id = $ref;
            $data->type = 2;
            $data->status = 1; //initiate
            $data->save();


            $message = Auth::user()->email . "| wants to fund |  NGN " . number_format($request->amount) . " | with ref | $ref |  on FaddedSMS";
            send_notification($message);


            return Redirect::to($url);
        }


        if ($request->type == 2) {

            if ($request->amount < 100) {
                return back()->with('error', 'You can not fund less than NGN 100');
            }


            if ($request->amount > 100000) {
                return back()->with('error', 'You can not fund more than NGN 100,000');
            }


            $ref = "VERFM" . random_int(000, 999) . date('ymdhis');
            $email = Auth::user()->email;


            $data = new Transaction();
            $data->user_id = Auth::id();
            $data->amount = $request->amount;
            $data->ref_id = $ref;
            $data->type = 2; //manual funding
            $data->status = 1; //initiate
            $data->save();


            $message = Auth::user()->email . "| wants to fund Manually |  NGN " . number_format($request->amount) . " | with ref | $ref |  on FaddedSMS";
            send_notification($message);


            $data['account_details'] = AccountDetail::where('id', 1)->first();
            $data['amount'] = $request->amount;

            return view('manual-fund', $data);
        }


    }

    public function fund_now_xtrapay(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|gt:0',
        ]);


        Transaction::where('user_id', Auth::id())->where('detail', null)->delete() ?? null;


        if ($request->type == 1) {

            try {

                if ($request->amount < 1000) {
                    return back()->with('error', 'You can not fund less than NGN 1000');
                }


                if ($request->amount > 100000) {
                    return back()->with('error', 'You can not fund more than NGN 100,000');
                }


                $key = env('XTRAPAY_ACCESS_KEY');
                do {
                    $ref = substr(str_shuffle(time() . mt_rand(100000, 999999)), 0, 12);
                    $exists = Transaction::where('ref_id', $ref)->exists();
                } while ($exists);

                $charge = 100 + (round($request->amount, 2) * 0.015);

                if ($request->amount >= 10000) {
                    $charge = 100 + (round($request->amount, 2) * 0.02);
                }

                $finalAmount = round($request->amount + $charge, 0);

                // API request using Bearer token
                $response = Http::withToken($key)->post("https://mobile.xtrapay.ng/api/faddedsocials/generateAccount", [
                    'reference' => $ref,
                    'amount' => $finalAmount,
                    'service' => 'fadded_sms'
                ]);

                $detail = null;

                if ($response->successful()) {
                    $responseData = $response->json();

                    if (isset($responseData['statusCode']) && $responseData['statusCode'] == 200 && isset($responseData['data'])) {
                        $accountData = $responseData['data'];

                        // Store virtual account details in deposit detail
                        $detail = [
                            'reference' => $accountData['reference'] ?? $ref,
                            'virtual_account' => $accountData['accountNumber'] ?? null,
                            'bank_name' => $accountData['bank'] ?? null,
                            'account_name' => $accountData['accountName'] ?? null,
                            'amount' => $accountData['amount'] ?? $finalAmount,
                        ];

                        $data = new Transaction();
                        $data->user_id = Auth::id();
                        $data->amount = $request->amount;
                        $data->method = 118;
                        $data->charge = $charge;
                        $data->final_amount = $finalAmount;
                        $data->detail = $detail;
                        $data->ref_id = $ref;
                        $data->type = 2;
                        $data->status = 1; //initiate
                        $data->save();

                        // Send webhook for pending transaction
                        WebhookService::sendPendingTransaction($data, Auth::user());

                        $message = Auth::user()->email . "| wants to fund |  NGN " . number_format($request->amount) . " | with ref | $ref |  on FaddedSMS";
                        send_notification($message);


                        return view('xtrapay', $detail);

                    } else {
                        return back()->with('error', 'Something went wrong, try again later');

                    }
                } else {
                    return back()->with('error', 'Something went wrong.');
                }
            } catch (\Exception $e) {
                echo $e;
                #return back()->with('error', 'Something went wrong, Contact Customer Service');
            }

        }


        if ($request->type == 2) {

            if ($request->amount < 100) {
                return back()->with('error', 'You can not fund less than NGN 100');
            }


            if ($request->amount > 100000) {
                return back()->with('error', 'You can not fund more than NGN 100,000');
            }


            $ref = "VERFM" . random_int(000, 999) . date('ymdhis');
            $email = Auth::user()->email;


            $data = new Transaction();
            $data->user_id = Auth::id();
            $data->amount = $request->amount;
            $data->ref_id = $ref;
            $data->type = 2; //manual funding
            $data->status = 1; //initiate
            $data->save();


            $message = Auth::user()->email . "| wants to fund Manually |  NGN " . number_format($request->amount) . " | with ref | $ref |  on FaddedSMS";
            send_notification($message);


            $data['account_details'] = AccountDetail::where('id', 1)->first();
            $data['amount'] = $request->amount;

            return view('manual-fund', $data);
        }


    }


    public function fund_manual_now(Request $request)
    {


        if ($request->receipt == null) {
            return back()->with('error', "Payment receipt is required");
        }


        $file = $request->file('receipt');
        $receipt_fileName = date("ymis") . $file->getClientOriginalName();
        $destinationPath = public_path() . 'upload/receipt';
        $request->receipt->move(public_path('upload/receipt'), $receipt_fileName);


        $pay = new ManualPayment();
        $pay->receipt = $receipt_fileName;
        $pay->user_id = Auth::id();
        $pay->amount = $request->amount;
        $pay->save();


        $message = Auth::user()->email . "| submitted payment receipt |  NGN " . number_format($request->amount) . " | on FaddedSMS";
        send_notification($message);

        return view('confirm-pay');
    }


    public function confirm_pay(Request $request)
    {
        return view('confirm-pay');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            if (Auth::user()->verify == 0) {
                $email = $request->email;
                $expiryTimestamp = time() + 24 * 60 * 60; // 24 hours in seconds
                $url = url('') . "/verify-account-now?code=$expiryTimestamp&email=$request->email";
                $username = User::where('email', $request->email)->first()->username ?? null;

                User::where('email', $email)->update([
                    'code' => $expiryTimestamp
                ]);

                $data = array(
                    'fromsender' => 'noreply@faddedsms.com', 'FaddedSMS',
                    'subject' => "Verify Account",
                    'toreceiver' => $email,
                    'url' => $url,
                    'user' => $username,
                );


                Mail::send('verify-account', ["data1" => $data], function ($message) use ($data) {
                    $message->from($data['fromsender']);
                    $message->to($data['toreceiver']);
                    $message->subject($data['subject']);
                });

                $username = Auth::user()->username;
                return back()->with('message', 'Account verification has been sent to your email, Verify your account');
            } else {
                return redirect('us');
            }
        }

        return back()->with('error', "Email or Password Incorrect");
    }


    public function verify_account_now(request $request)
    {
        $storedExpiryTimestamp = $request->code;
        ;
        if (time() >= $storedExpiryTimestamp) {

            $user = Auth::id() ?? null;
            $email = $request->email;
            return view('expired', compact('user', 'email'));

        } else {

            User::where('email', $request->email)->update(['verify' => 2]);
            return view('Auth.verify-account-now');
        }

    }

    public function verify_account_now_page(request $request)
    {
        return view('verify-account-now-success');

    }








    public function verify_password(request $request)
    {

        $code = User::where('email', $request->email)->first()->code;


        $storedExpiryTimestamp = $request->code;
        ;

        if (time() >= $storedExpiryTimestamp) {

            $user = Auth::id() ?? null;
            $email = $request->email;
            return view('expired', compact('user', 'email'));
        } else {

            $user = Auth::id() ?? null;
            $email = $request->email;

            return view('verify-password', compact('user', 'email'));
        }
    }


    public function register_index(Request $request)
    {
        return view('Auth.register');
    }


    public function login_index(Request $request)
    {
        return view('Auth.login');
    }


    public function forget_password(Request $request)
    {
        return view('Auth.forgot-password');
    }


    public function register(Request $request)
    {
        // Validate the user input
        $validatedData = $request->validate([
            'username' => 'required||string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:4|confirmed',
        ]);

        // Create a new user
        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);


        $email = $request->email;
        $expiryTimestamp = time() + 24 * 60 * 60; // 24 hours in seconds
        $url = url('') . "/verify-account-now?code=$expiryTimestamp&email=$request->email";
        $username = $validatedData['username'];

        User::where('email', $email)->update([
            'code' => $expiryTimestamp
        ]);

        $data = array(
            'fromsender' => 'noreply@faddedsms.com', 'FaddedSMS',
            'subject' => "Verify Account",
            'toreceiver' => $email,
            'url' => $url,
            'user' => $username,
        );


        Mail::send('verify-account', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });

        return back()->with('message', 'Account verification has been sent to your email, Verify your account');




        //auth()->login($user);

        return redirect('login');
    }


    public function profile(request $request)
    {


        $user = Auth::id();
        $orders = SoldLog::latest()->where('user_id', Auth::id())->paginate(5);


        return view('profile', compact('user', 'orders'));
    }


    public function logout(Request $request)
    {

        Auth::logout();
        return redirect('/');
    }


    public function change_password(request $request)
    {

        $user = Auth::id();


        return view('change-password', compact('user'));
    }


    public function faq(request $request)
    {
        $user = Auth::id();
        return view('faq', compact('user'));
    }

    public function terms(request $request)
    {
        $user = Auth::id();
        return view('terms', compact('user'));
    }

    public function rules(request $request)
    {
        $user = Auth::id();
        return view('rules', compact('user'));
    }


    public function update_password_now(request $request)
    {
        // Validate the user input
        $validatedData = $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);

        User::where('id', Auth::id())->update([
            'password' => Hash::make($validatedData['password']),
        ]);

        // Redirect the user to a protected route or dashboard
        return back()->with('message', 'Password Changed Successfully');
    }


    public function reset_password(request $request)
    {

        $email = $request->email;
        $expiryTimestamp = time() + 24 * 60 * 60; // 24 hours in seconds
        $url = url('') . "/verify-password?code=$expiryTimestamp&email=$request->email";

        $ck = User::where('email', $request->email)->first()->email ?? null;
        $username = User::where('email', $request->email)->first()->username ?? null;


        if ($ck == $request->email) {

            User::where('email', $email)->update([
                'code' => $expiryTimestamp
            ]);

            $data = array(
                'fromsender' => 'noreply@faddedsms.com', 'FaddedSMS',
                'subject' => "Reset Password",
                'toreceiver' => $email,
                'url' => $url,
                'user' => $username,
            );


            Mail::send('reset-password-mail', ["data1" => $data], function ($message) use ($data) {
                $message->from($data['fromsender']);
                $message->to($data['toreceiver']);
                $message->subject($data['subject']);
            });


            return redirect('/forgot-password')->with('message', "A reset password mail has been sent to $request->email, if not inside inbox check your spam folder");
        } else {
            return back()->with('error', 'Email can not be found on our system');
        }
    }




    public function expired(request $request)
    {
        $user = Auth::id() ?? null;
        return view('expired', compact('user'));
    }

    public function reset_password_now(request $request)
    {

        $validatedData = $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);


        $password = Hash::make($validatedData['password']);

        User::where('email', $request->email)->update([

            'password' => $password

        ]);

        return redirect('/login')->with('message', 'Password reset successful, Please login to continue');
    }


    public function get_smscode(request $request)
    {


        $sms = Verification::where('phone', $request->num)->first()->sms ?? null;
        $order_id = Verification::where('phone', $request->num)->first()->order_id ?? null;
        check_sms($order_id);

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

        $activationId = $request->activationId;
        $messageId = $request->messageId;
        $service = $request->service;
        $text = $request->text;
        $code = $request->code;
        $country = $request->country;
        $receivedAt = $request->receivedAt;
        $orders = Verification::where('order_id', $activationId)->update(['sms' => $code, 'status' => 2]);


        $message = json_encode($request->all());
        send_notification($message);


    }


    public function world_webhook(request $request)
    {

        $activationId = $request->orderid;
        $messageId = $request->messageId;
        $service = $request->service;
        $text = $request->text;
        $code = $request->sms;
        $country = $request->country;
        $receivedAt = $request->receivedAt;
        $orders = Verification::where('order_id', $activationId)->update(['sms' => $code, 'status' => 2]);


        $message = json_encode($request->all());
        send_notification($message);


    }


    public function diasy_webhook(request $request)
    {

        $activationId = $request->activationId;
        $messageId = $request->messageId;
        $service = $request->service;
        $text = $request->text;
        $code = $request->sms;
        $country = $request->country;
        $receivedAt = $request->receivedAt;

        $orders = Verification::where('order_id', $activationId)->update(['sms' => $code, 'status' => 2]);

        $message = json_encode($request->all());
        send_notification($message);


    }


    public function orders(request $request)
    {
        $data['verifications']  = Verification::latest()->where('user_id', Auth::id())->get() ?? null;
        $data['services'] = get_services();
        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['orders'] = Verification::latest()->where('user_id', Auth::id())->paginate('10');


        return view('orders', $data);
    }


    public function about_us(request $request)
    {

        return view('about-us');
    }


    public function policy(request $request)
    {

        return view('policy');
    }


    public function delete_order(request $request)
    {
        $order = Verification::where('id', $request->id)->first() ?? null;

        if ($order->status == 1 && $order->type == 2) {

            $orderID = $order->order_id;
            $can_order = cancel_world_order($orderID);


            if ($can_order == 0) {
                return back()->with('error', "Please wait and try again later");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                $email = User::where('id', $order->user_id)->first()->email ?? null;
                $balance = User::where('id', $order->user_id)->first()->wallet ?? null;
                $bb = number_format($balance, 2);
                $message = $email. "| just canceled | $order->service | type is $order->type | $amount is refunded | Balance is  $bb";
                send_notification($message);


                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been canceled");
            }
        }

        if ($order->status == 1 && $order->type == 1) {

            $order = Verification::where('id', $request->id)->first() ?? null;


            if ($order == null) {
                return redirect('home')->with('error', 'Order not found');
            }

            if ($order->status == 2) {
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been successfully deleted");
            }

            if ($order->status == 1) {

                $orderID = $order->order_id;
                $corder = cancel_order($orderID);

                if ($corder == 0) {
                    return back()->with('error', "Please wait and try again later");
                }


                if ($corder == 1) {
                    $amount = number_format($order->cost, 2);
                    Verification::where('id', $request->id)->delete();
                    User::where('id', Auth::id())->increment('wallet', $order->cost);
                    $email = User::where('id', $order->user_id)->first()->email ?? null;
                    $balance = User::where('id', $order->user_id)->first()->wallet ?? null;
                    $bb = number_format($balance, 2);
                    $message = $email. "| just canceled | $order->service | type is $order->type | $amount is refunded | Balance is  $bb";
                    send_notification($message);

                    return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
                }


            }

        }

        if ($order->status == 1 && $order->type == 3) {

            $order = Verification::where('id', $request->id)->first() ?? null;


            if ($order == null) {
                return redirect('home')->with('error', 'Order not found');
            }

            if ($order->status == 2) {
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been successfully deleted");
            }

            if ($order->status == 1) {

                $token = env('SIMTOKEN');
                $ch = curl_init();
                $id = Verification::where('id', $request->id)->first()->order_id ?? null;
                $cost = Verification::where('id', $request->id)->first()->cost ?? null;
                $user_id = Verification::where('id', $request->id)->first()->user_id ?? null;

                if ($id == null) {
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

                if ($status == "CANCELED") {

                    Verification::where('id', $request->id)->delete();
                    User::where('id', $user_id)->increment('wallet', $cost);
                    return back()->with('message', "Number Canceled, NGN $cost has been refunded");

                }

                $status = Verification::where('id', $request->id)->first()->status ?? null;

                if ($status != null && $status != 2) {
                    Verification::where('id', $request->id)->delete();
                    User::where('id', $user_id)->increment('wallet', $cost);
                    return back()->with('message', "Number Canceled, NGN $cost has been refunded");
                }




            }

        }



    }


    public function e_check(request $request)
    {

        $get_user = User::where('email', $request->email)->first() ?? null;

        if ($get_user == null) {

            return response()->json([
                'status' => false,
                'message' => 'No user found, please check email and try again',
            ]);
        }


        return response()->json([
            'status' => true,
            'user' => $get_user->username,
        ]);
    }


    public function e_fund(request $request)
    {


        $ipb = env('IPA');
        $ipa = env('IPB');
        $ip = $request->ip();


        if ($ip == $ipb || $ip == $ipa || $request->fund == "fund") {

            $get_user = User::where('email', $request->email)->first() ?? null;

            if ($get_user == null) {

                return response()->json([
                    'status' => false,
                    'message' => 'No one user found, please check email and try again',
                ]);
            }

            $ip = $request->ip();
            $url = $request->url();
            $message = $request->email . "| just just funded wallet on FADDEDSMS | $ip | NGN" . $request->amount;
            send_notification($message);



            User::where('email', $request->email)->increment('wallet', $request->amount) ?? null;

            $amount = number_format($request->amount, 2);

            $get_depo = Transaction::where('ref_id', $request->order_id)->first() ?? null;
            if ($get_depo == null) {
                $trx = new Transaction();
                $trx->ref_id = $request->order_id;
                $trx->user_id = $get_user->id;
                $trx->status = 2;
                $trx->amount = $request->amount;
                $trx->type = 2;
                $trx->save();
            } else {
                Transaction::where('ref_id', $request->order_id)->update(['status' => 2]);
            }


            return response()->json([
                'status' => true,
                'message' => "NGN $amount has been successfully added to your wallet",
            ]);

        } else {
            $ip = $request->ip();
            $url = $request->url();
            $message = $request->email . "| just is trying to fund wallet on FADDEDSMS | $ip | $url | NGN" . $request->amount;
            send_notification($message);
        }

        return response()->json([
            'status' => true,
            'message' => "Something went wrong",
        ]);


    }

    public function verify_username(request $request)
    {

        $get_user = User::where('email', $request->email)->first() ?? null;

        if ($get_user == null) {

            return response()->json([
                'username' => "Not Found, Pleas try again"
            ]);

        }

        return response()->json([
            'username' => $get_user->username
        ]);


    }

}
