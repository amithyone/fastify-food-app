@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">
        <div class="container title my-5">
            <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
                <div class="col-md-8 col-xl-6">
                    <h4 class="mb-3 text-danger">Hi {{ Auth::user()->username }},</h4>
                    <p class="mb-0">
                        Connect our services to your website with API.
                    </p>
                </div>
            </div>
        </div>


        <div class="container technology-block">

            <div class="row p-3">
                <div class="col-xl-12 col-md-6 col-sm-12">
                    <div class="card border-0 shadow-lg p-3 mb-5 bg-body rounded-40">
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session()->has('message'))
                                <div class="alert alert-success">
                                    {{ session()->get('message') }}
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger">
                                    {{ session()->get('error') }}
                                </div>
                            @endif

                            <div class="card-title">
                                API KEY
                            </div>


                            <form action="set-webhook" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-8">
                                        <input type="text" name="amount" class="form-control" name="api_key"
                                               value="{{$api_key}}" disabled>

                                        <div class="col-4 mb-3">
                                            <a href="/generate-token" class="btn btn-dark btn mt-2">Generate Api Key</a>
                                        </div>
                                    </div>


                                </div>

                                <div class="row">
                                    <div class="col-xl-8 col-sm-12">
                                        <label class="my-3">Webhook URL</label>
                                        <input type="text" name="webhook" class="form-control" name="webhook_url"
                                               value="{{$webhook_url}}">

                                        <div class="col-xl-4  col-sm-12">
                                            <button type="submit" style="background: #0a0c0d"
                                                    class="btn btn mt-3 text-white">Set Webhook
                                            </button>
                                        </div>
                                    </div>


                                </div>

                            </form>


                        </div>

                    </div>
                </div>

                <div class="col-lg-12 col-sm-12">
                    <div class="card border-0 shadow-lg p-3 mb-5 bg-body rounded-40">

                        <div class="card-body">


                            <div class="">

                                <div>


                                    <h3 class="text-3xl font-semibold mb-4 text-center">API</h3>

                                    <h4 class="text-2xl my-4">General info</h4>

                                    <p class="py-2">Integrate with our Api with below requests</p>

                                    <p class="py-2">In order to make API calls, you need to provide your API token in
                                        the X-ApiKey header, or as api_key in the URL.</p>

                                    <p class="py-2">You can copy your API key above.

                                    <h4 class="text-2xl mt-8">Getting Wallet Balance</h4>

                                    <div class="mockup-code my-8">
                                        <pre><code>GET "{{url('')}}/api/balance?api_key={{$api_key}}&action=balance"</code></pre>
                                        <pre></pre>
                                        <pre><code>#Success Response = {'status' => true,'main_balance' => 200}</code></pre>
                                        <pre><code>#Failed Response: Wrong or Bad Api key</code></pre>
                                    </div>

                                    <h4 class="text-2xl mt-8 mb-4">Get World countries</h4>
                                    <div class="mockup-code my-8">
                                        <pre><code>GET "{{url('')}}/api/get-world-countries?api_key={{$api_key}}&action=get-world-countries"</code></pre>
                                        <pre></pre>
                                        <pre><code>#Success Response = {"status":true,"data":[{"ID":1,"name":"United States","short_name":"US","cc":"1","region":"North America"},</code></pre>
                                        <pre><code>#Failed Response: Wrong or Bad Api key</code></pre>
                                    </div>


                                    <h4 class="text-2xl mt-8 mb-4">Get World Services</h4>
                                    <div class="mockup-code my-8">
                                        <pre><code>GET "{{url('')}}/api/get-world-services?api_key={{$api_key}}&action=get-world-services"</code></pre>
                                        <pre></pre>
                                        <pre><code>#Success Response = {"status":true,"data":[{"ID":1,"name":"1688","favourite":0},</code></pre>
                                        <pre><code>#Failed Response: Wrong or Bad Api key</code></pre>
                                    </div>


                                    <h4 class="text-2xl mt-8 mb-4">Check World Number Availability</h4>
                                    <div class="mockup-code my-8">
                                        <pre><code>GET "{{url('')}}/api/check-world-number-availability?api_key={{$api_key}}&action=check-availability&country=US&service=1"</code></pre>
                                        <pre></pre>
                                        <pre><code>#Success Response = {"status":true,"cost":500,"stock":8444,"country":"US","service":"1"}</code></pre>
                                        <pre><code>#Failed Response: Wrong or Bad Api key</code></pre>
                                    </div>

                                    <h4 class="text-2xl mt-8 mb-4">Rent World Number</h4>
                                    <div class="mockup-code my-8">
                                        <pre><code>GET "{{url('')}}/api/rent-world-number?api_key={{$api_key}}&action=rent-world-number&country=US&service=1"</code></pre>
                                        <pre></pre>
                                        <pre><code>#Success Response = {"status":true,"order_id":389,"phone_no":"19362441517","country":"US","service":"1012"}</code></pre>
                                        <pre><code>#Failed Response: Wrong or Bad Api key</code></pre>
                                    </div>


                                    <h4 class="text-2xl mt-8 mb-4">Get World SMS</h4>
                                    <div class="mockup-code my-8">
                                        <pre><code>GET "{{url('')}}/api/get-world-sms?api_key={{$api_key}}&action=get-world-sms&order_id=389"</code></pre>
                                        <pre></pre>
                                        <pre><code>#Success Response = {"status":true,"sms_status":"COMPLETED","full_sms":"Do not send code 123456 to anyone","code":"123456","country":"United States","service":"WhatsApp"}</code></pre>
                                        <pre><code>#Failed Response: Wrong or Bad Api key</code></pre>
                                    </div>

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            You'll need to have sufficient balance and know the shortcode of the service. You can get the shortcode from the <a href="https://daisysms.com/dashboard/services" class="link">Services page</a>.--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            You can also specify max_price - the maximum price you're willing to pay for the number. The request will fail with MAX_PRICE_EXCEEDED if the current price of the service is higher than max_price. max_price is to be specified in dollars, e.g. max_price=0.50--}}
                                    {{--                                            means 50 cents. To ensure compatibility with sms-activate, maxPrice can be used as an alias of max_price.--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            For example: the current price of service X is $0.50. We see a surge in demand where numbers get sold out instantly and we increase the price to $0.60. If you have set max_price to $0.50, you'll get an error on the next API call. However if we see the--}}
                                    {{--                                            demand at $0.50 is low, we will decrease the price to $0.40. In this case your API calls will work and you will only pay $0.40 for the number.--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <p class="font-bold py-2">--}}
                                    {{--                                            We recommend that you include max_price with every request. This could help you prevent unexpected expenses in case we raise the price.--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <p class="py-2">Each successful rental also has X-Price header set to the effective price of the rented number.</p>--}}

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            You can specify preferred area codes in the areas query string parameter. Such rentals are subject to a 20% price increase. For example, areas=212,718 will only rent numbers with area codes 212 or 718.--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code>curl "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=getNumber&amp;service=ds&amp;max_price=5.5"</code></pre>--}}
                                    {{--                                            <pre></pre>--}}
                                    {{--                                            <pre><code># Got the number: ACCESS_NUMBER:999999:13476711222</code></pre>--}}
                                    {{--                                            <pre><code># Max price exceeded: MAX_PRICE_EXCEEDED</code></pre>--}}
                                    {{--                                            <pre><code># No numbers left: NO_NUMBERS</code></pre>--}}
                                    {{--                                            <pre><code># Need to finish some rentals before renting more: TOO_MANY_ACTIVE_RENTALS</code></pre>--}}
                                    {{--                                            <pre><code># Not enough balance left: NO_MONEY</code></pre>--}}
                                    {{--                                            <pre><code></code></pre>--}}
                                    {{--                                            <pre><code># Example with area codes</code></pre>--}}
                                    {{--                                            <pre><code>curl "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=getNumber&amp;service=ds&amp;max_price=5.5&amp;areas=201,520"</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <h4 class="text-2xl mt-8 mb-4">Getting the code</h4>--}}

                                    {{--                                        <p class="py-2">You'll need the ID that you got from the rent number response. Please poll every 3 seconds or more.</p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code># GET https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=getStatus&amp;id=$ID</code></pre>--}}
                                    {{--                                            <pre></pre>--}}
                                    {{--                                            <pre><code>curl "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=getStatus&amp;id=308"</code></pre>--}}
                                    {{--                                            <pre></pre>--}}
                                    {{--                                            <pre><code># Got code: STATUS_OK:12345</code></pre>--}}
                                    {{--                                            <pre><code># Wrong ID: NO_ACTIVATION</code></pre>--}}
                                    {{--                                            <pre><code># Waiting for SMS: STATUS_WAIT_CODE</code></pre>--}}
                                    {{--                                            <pre><code># Rental cancelled: STATUS_CANCEL</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <h4 class="text-2xl mt-8 mb-4">Marking rental as done</h4>--}}

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            Some services support getting multiple codes within your rental timeframe. Once you're done with the number and don't need to receive any more SMS to it, we suggest that you mark it as "done". This helps our service and makes sure you don't run out of--}}
                                    {{--                                            the limit of numbers waiting for SMS at the same time.--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code># GET https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=setStatus&amp;id=$ID&amp;status=6</code></pre>--}}
                                    {{--                                            <pre></pre>--}}
                                    {{--                                            <pre><code>curl "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=setStatus&amp;id=308&amp;status=6"</code></pre>--}}
                                    {{--                                            <pre></pre>--}}
                                    {{--                                            <pre><code># Success: ACCESS_ACTIVATION</code></pre>--}}
                                    {{--                                            <pre><code># Failure (rental missing): NO_ACTIVATION</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <h4 class="text-2xl mt-8 mb-4">Cancelling a rental</h4>--}}

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            You can cancel a rental and receive the locked money back to your main balance. Please refrain from looping through numbers without using them since it may get your account banned.--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code># GET https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=setStatus&amp;id=$ID&amp;status=8</code></pre>--}}
                                    {{--                                            <pre></pre>--}}
                                    {{--                                            <pre><code>curl "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=setStatus&amp;id=308&amp;status=8"</code></pre>--}}
                                    {{--                                            <pre></pre>--}}
                                    {{--                                            <pre><code># Success: ACCESS_CANCEL</code></pre>--}}
                                    {{--                                            <pre><code># Failure (rental missing or already got the code): ACCESS_READY</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <h4 class="text-2xl mt-8 mb-4">Getting a list of services with prices</h4>--}}

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            There are 2 ways to get the list of services along with info about remaining numbers, prices, and whether the service can receive more than one sms. If there are more than 100 numbers remaining, you are not shown the exact amount. For example if there--}}
                                    {{--                                            are 55 number remaining, you will see "55" in the response. If there are 155 numbers remaining, you will see "100". 187 is the code for USA in sms-activate API.--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <p class="py-2">Getting an object that goes: service =&gt; country =&gt; data:</p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code>curl "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=getPricesVerification"</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <p class="py-2">Getting an object that goes: country =&gt; service =&gt; data:</p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code>curl "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&amp;action=getPrices"</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <h4 class="text-2xl mt-8 mb-4" id="webhook">Webhooks</h4>--}}

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            You can set a webhook URL on the <a href="https://daisysms.com/dashboard/profile" class="link">profile page</a>. If you do so, incoming SMS will be forwarded in a POST request to that address. If your server doesn't respond with a 2xx status--}}
                                    {{--                                            code, the attempt will be retried 15 seconds later for a maximum of 8 times. The timeout for the request is 3 seconds.--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            The format of the message is as follows:--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code>{</code></pre>--}}
                                    {{--                                            <pre><code>    "activationId": 123,</code></pre>--}}
                                    {{--                                            <pre><code>    "messageId": 999,</code></pre>--}}
                                    {{--                                            <pre><code>    "service": "go",</code></pre>--}}
                                    {{--                                            <pre><code>    "text": "Your sms text",</code></pre>--}}
                                    {{--                                            <pre><code>    "code": "Your sms code",</code></pre>--}}
                                    {{--                                            <pre><code>    "country": 0,</code></pre>--}}
                                    {{--                                            <pre><code>    "receivedAt": "2022-06-01 17:30:57"</code></pre>--}}
                                    {{--                                            <pre><code>}</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <p class="py-2">--}}
                                    {{--                                            (The date is in UTC)--}}
                                    {{--                                        </p>--}}

                                    {{--                                        <h4 class="text-2xl mt-8 mb-4" id="multi">Getting numbers in batches</h4>--}}

                                    {{--                                        <p class="py-2">You can get multiple numbers in 1 request in the following format:</p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code>13479111111----https://daisysms.com/stubs/multi/result?id=1005&amp;apikey=YOUR_API_KEY_HERE</code></pre>--}}
                                    {{--                                            <pre><code>13479111112----https://daisysms.com/stubs/multi/result?id=1006&amp;apikey=YOUR_API_KEY_HERE</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <p class="py-2">Use the following URL. Replace $APIKEY with your API key.</p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code>https://daisysms.com/stubs/multi/reserve?apikey=$APIKEY&amp;service=mb&amp;count=2&amp;max_price=0.25</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <p class="py-2">The URL provided with the number will contain text "WAITING" on no SMS, and the following JSON on SMS:</p>--}}

                                    {{--                                        <div class="mockup-code my-8">--}}
                                    {{--                                            <pre><code>{"code":200,"message":"success","data":"full message here"}</code></pre>--}}
                                    {{--                                        </div>--}}

                                    {{--                                        <p class="py-2">The same restrictions apply as with the other methods.</p>--}}
                                    {{--                                    </div>--}}


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection
