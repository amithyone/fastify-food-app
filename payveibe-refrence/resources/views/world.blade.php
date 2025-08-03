@extends('layout.main')
@section('content')


    <div class="row">
        <div class="col-md-6 col-xl-6 col-sm-12">
            <div class="card">
                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasRight"
                     aria-labelledby="offcanvasRightLabel">

                    <div class="offcanvas-body">


                        <div class="">

                            <div class="p-2 col-lg-6">
                                <input type="text" id="searchInput" class="form-control"
                                       placeholder="Search for a service..."
                                       onkeyup="filterServices()">
                            </div>


                            <div class="row my-3 p-1 text-white"
                                 style="background: #dedede; border-radius: 10px; font-size: 10px; border-radius: 12px">
                                <div class="col-5">
                                    <h5 class="mt-2">Services</h5>
                                </div>
                                <div class="col">
                                    <h5 class="mt-2">Price</h5>
                                </div>
                            </div>


                        </div>


                        <div style="height:700px; width:100%; overflow-y: scroll;" class="p-2">


                            @foreach ($services as $key => $value)
                                <div class="row service-row text-white">
                                    @foreach ($value as $innerKey => $innerValue)
                                        <div style="font-size: 11px"
                                             class="col-5 service-name d-flex justify-content-start">
                                            🇺🇸 {{ $innerValue->name }}
                                        </div>

                                        <div style="font-size: 11px" class="col">
                                            @php $cost = $get_rate * $innerValue->cost + $margin  @endphp
                                            <strong>N{{ number_format($cost, 2) }}</strong>
                                        </div>

                                        <div style="font-size: 11px" class="col">

                                        </div>


                                        <div class="col mr-3">
                                            @auth
                                                <form action="order-usano" method="POST">
                                                    <input hidden name="service"
                                                           value="{{ $key }}">
                                                    <input hidden name="price"
                                                           value="{{ $cost }}">
                                                    <input hidden name="cost"
                                                           value="{{ $innerValue->cost }}">
                                                    <input hidden name="name"
                                                           value="{{ $innerValue->name }}">
                                                    <button class="myButton"
                                                            style="border: 0px; background: transparent"
                                                            onclick="hideButton(this)"><i
                                                            class="fa fa-shopping-bag"></i>
                                                    </button>
                                                </form>
                                            @else

                                                <a class=""
                                                   href="/login">
                                                    <i class="fa fa-lock text-dark"> Login</i>
                                                </a>
                                            @endauth


                                            <script>
                                                function hideButton(link) {
                                                    // Hide the clicked link
                                                    link.style.display = 'none';

                                                    setTimeout(function () {
                                                        link.style.display = 'inline'; // or 'block' depending on your layout
                                                    }, 5000); // 5 seconds
                                                }
                                            </script>


                                        </div>


                                        <hr style="border-color: #cccccc" class=" my-2">
                                    @endforeach
                                </div>
                            @endforeach


                        </div>

                    </div>
                </div>


            </div>
        </div>
    </div>


    <section id="technologies mt-4 my-5">
        <div class="container title my-5">
            <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
                <div class="col-md-8 col-xl-6">
                    <h4 class="mb-3 text-danger">Hi {{ Auth::user()->username }} 👋</h4>
                    <p class="mb-0">
                        <a href="fund-wallet"
                           class="btn btn-dark mb-4">NGN {{ number_format(Auth::user()->wallet, 2) }}</a>
                    </p>
                    <p class="mb-2">
                        What will you like to do ?
                    </p>
                    {{--                    <button--}}
                    {{--                        class="btn btn-light-secondary my-3"--}}
                    {{--                        type="button"--}}
                    {{--                        data-bs-toggle="offcanvas"--}}
                    {{--                        data-bs-target="#offcanvasRight"--}}
                    {{--                        aria-controls="offcanvasRight"--}}
                    {{--                        style="background: #064174"--}}
                    {{--                    >--}}
                    {{--                        USA SERVICES--}}
                    {{--                    </button>--}}


                    @include('servermenu')
                </div>
            </div>
        </div>




        <div class="container technology-block">


            <div class="row">
                <div class="col-xl-6 col-md-6 col-sm-12 my-3">
                    <div class="card " style="background: #064174; color: #ffffff">
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


                            <form action="check-av" method="POST">
                                @csrf

                                <div class="row">

                                    <div class="col-xl-10 col-md-10 col-sm-12 p-3">


                                        <label for="country" class="mb-2  mt-3 text-white">Choose
                                            Country</label>
                                        <div>
                                            <select style="border-color:rgb(0, 11, 136); padding: 10px" class="w-100"
                                                    id="dropdownMenu" class="dropdown-content" name="country">
                                                <option style="background: black" value=""> Select Country</option>
                                                @foreach ($wcountries as $data)
                                                    <option value="{{ $data['ID'] }}">{{ $data['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <label for="country" class="mt-3 mb-2  mt-3 text-white">Choose
                                            Services</label>
                                        <div>
                                            <select class="form-control w-100" id="select_page2" name="service">

                                                <option value=""> Select Service</option>
                                                @foreach ($wservices as $data)
                                                    <option value="{{ $data['ID'] }}">{{ $data['name'] }}</option>
                                                @endforeach

                                            </select>
                                        </div>


                                        <button style="border: 0px; background: rgb(252,101,7); color: white;"
                                                type="submit"
                                                class="btn btn btn-lg w-100 mt-3 border-0">Check
                                            availability
                                        </button>


                                    </div>
                                </div>
                            </form>


                        </div>

                    </div>
                </div>


                @if ($product != null)
                    <div class="col-xl-6 col-md-6 col-sm-12 p-3">
                        <div class="card mb-3">
                            <div class="card-body">

                                <div class="row">
                                    <p class="text-muted text-center">Service Information</p>

                                    <h5 class="text-center my-2">Amount</h5>
                                    <h6 class="text-center text-muted my-2 mb-4">Price:
                                        NGN {{ number_format($price, 2) }}</h6>


                                    <h5 class="text-center my-2">Available Nuumbers</h5>
                                    <h6 class="text-center text-muted my-2 mb-4">
                                        {{ number_format($stock, 2)}}</h6>

                                    <h5 class="text-center text-muted my-2">Success rate: <span
                                            style="font-size: 30px; color: rgb(63,63,63);"> @if ($rate < 10)
                                                {{ $rate }}%
                                            @elseif ($rate < 20)
                                                {{ $rate }}%
                                            @elseif ($rate < 30)
                                                {{ $rate }}%
                                            @elseif ($rate < 40)
                                                {{ $rate }}%
                                            @elseif ($rate < 50)
                                                {{ $rate }}%
                                            @elseif ($rate < 60)
                                                {{ $rate }}%
                                            @elseif ($rate < 70)
                                                {{ $rate }}%
                                            @elseif ($rate < 80)
                                                {{ $rate }}%

                                            @elseif ($rate < 90)
                                                {{ $rate }}%
                                            @elseif ($rate <= 100)
                                                {{ $rate }}%
                                            @else
                                            @endif</span></h5>
                                    <h6></h6>


                                    @if (Auth::user()->wallet < $price && $stock > 0)
                                        <a href="fund-wallet" class="btn btn-secondary text-white btn-lg">Fund
                                            Wallet</a>
                                    @elseif($stock > 0 && Auth::user()->wallet > $price)
                                        <form action="order_now" method="POST">
                                            @csrf

                                            <input type="text" name="country" hidden value="{{ $count_id ?? null }}">
                                            <input type="text" name="price" hidden value="{{ $price ?? null }}">
                                            <input type="text" name="service" hidden value="{{ $serv ?? null }}">


                                            <button type="submit"
                                                    style="border: 0px; background: rgb(63,63,63); color: white;"
                                                    class="mb-2 btn btn w-100 btn-lg mt-6">Buy Number
                                                Now
                                            </button>


                                            <p class="text-muted text-center my-5">
                                                At FADDEDSMS, we prioritize quality, ensuring that you receive the
                                                highest standard of SMS verifications for all your needs. Our commitment
                                                to excellence means we only offer non-VoIP phone numbers, guaranteeing
                                                compatibility with any service you require.
                                            </p>


                                        </form>
                                    @else

                                        <a href="/home" class="btn btn-danger text-white btn-lg">Number not
                                            available</a>

                                    @endif


                                </div>


                            </div>

                        </div>
                    </div>
                @endif


                <div class="col-xl-6 col-md-6 col-sm-12 my-3">


                    {{--                    @if($q_orderuk > 1)--}}
                    {{--                        <div class="">--}}
                    {{--                            <p>Quick Order</p>--}}

                    {{--                            <form action="order_now" method="POST" >--}}
                    {{--                                @csrf--}}

                    {{--                                <input type="text" name="country" hidden value="2">--}}
                    {{--                                <input type="text" name="price" hidden value="{{ $ukamont ?? null }}">--}}
                    {{--                                <input type="text" name="service" hidden value="1012">--}}


                    {{--                                <button type="submit" style="background: rgba(194,194,194,0.59); padding: 8px; border-radius: 15px; color: black" class="border-0" >--}}
                    {{--                                    <svg width="50" height="40" viewBox="0 0 123 63" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
                    {{--                                        <ellipse cx="90.5" cy="31.5" rx="32.5" ry="31.5" fill="white"/>--}}
                    {{--                                        <ellipse cx="32.5" cy="31.5" rx="32.5" ry="31.5" fill="white"/>--}}
                    {{--                                        <g clip-path="url(#clip0_311_437)">--}}
                    {{--                                            <path d="M68.9816 30.2426C68.9805 33.8554 69.9983 37.3831 71.9335 40.4924L68.7965 51.1161L80.5179 48.2654C83.7599 49.9024 87.3923 50.7602 91.0835 50.7604H91.0932C103.279 50.7604 113.198 41.5633 113.203 30.2588C113.206 24.7809 110.908 19.6299 106.733 15.7546C102.559 11.8796 97.0073 9.74444 91.0923 9.74194C78.9054 9.74194 68.9868 18.9386 68.9818 30.2426" fill="url(#paint0_linear_311_437)"/>--}}
                    {{--                                            <path d="M68.1923 30.236C68.191 33.9788 69.2452 37.6327 71.2495 40.8533L68 51.8578L80.1417 48.905C83.4871 50.5968 87.2537 51.4888 91.0864 51.4902H91.0963C103.719 51.4902 113.995 41.9622 114 30.253C114.002 24.5783 111.622 19.2422 107.298 15.228C102.973 11.2143 97.2231 9.00233 91.0963 9C78.4715 9 68.1973 18.5267 68.1923 30.236ZM75.4231 40.2987L74.9697 39.6312C73.064 36.8205 72.0581 33.5725 72.0595 30.2373C72.0635 20.5048 80.6029 12.5867 91.1035 12.5867C96.1887 12.5887 100.968 14.4273 104.562 17.7633C108.156 21.0997 110.134 25.5347 110.133 30.2517C110.128 39.9842 101.589 47.9033 91.0963 47.9033H91.0888C87.6724 47.9017 84.3217 47.0507 81.3997 45.4425L80.7043 45.06L73.4992 46.8122L75.4231 40.2987Z" fill="url(#paint1_linear_311_437)"/>--}}
                    {{--                                            <path d="M85.3718 21.358C84.943 20.4742 84.4918 20.4563 84.0841 20.4408C83.7503 20.4275 83.3686 20.4285 82.9873 20.4285C82.6057 20.4285 81.9856 20.5617 81.4614 21.0925C80.9367 21.6238 79.4583 22.9078 79.4583 25.5193C79.4583 28.1308 81.509 30.6548 81.7949 31.0093C82.0812 31.3632 85.754 36.8938 91.5708 39.0215C96.4051 40.7897 97.3889 40.438 98.4381 40.3493C99.4875 40.261 101.824 39.0657 102.301 37.8262C102.778 36.5868 102.778 35.5245 102.635 35.3025C102.492 35.0813 102.11 34.9485 101.538 34.6832C100.966 34.4178 98.1519 33.1335 97.6274 32.9563C97.1027 32.7793 96.7212 32.691 96.3395 33.2225C95.9579 33.7532 94.862 34.9485 94.5279 35.3025C94.1942 35.6573 93.8602 35.7015 93.2881 35.436C92.7154 35.1697 90.8724 34.6098 88.6857 32.8017C86.9845 31.3947 85.8359 29.6572 85.502 29.1257C85.1682 28.595 85.4663 28.3073 85.7532 28.0428C86.0104 27.805 86.3257 27.423 86.6121 27.1132C86.8975 26.8032 86.9927 26.582 87.1836 26.228C87.3746 25.8737 87.279 25.5637 87.1361 25.2982C86.9927 25.0327 85.8806 22.4075 85.3718 21.358Z" fill="white"/>--}}
                    {{--                                        </g>--}}
                    {{--                                        <mask id="mask0_311_437" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="12" y="12" width="41" height="41">--}}
                    {{--                                            <path d="M32.5 53C43.8218 53 53 43.8218 53 32.5C53 21.1782 43.8218 12 32.5 12C21.1782 12 12 21.1782 12 32.5C12 43.8218 21.1782 53 32.5 53Z" fill="white"/>--}}
                    {{--                                        </mask>--}}
                    {{--                                        <g mask="url(#mask0_311_437)">--}}
                    {{--                                            <path d="M12 12L12.6406 13.7617L12 15.6035V17.4453L14.5625 21.7695L12 26.0938V28.6562L14.5625 32.5L12 36.3438V38.9062L14.5625 43.2305L12 47.5547V53L13.7617 52.3594L15.6035 53H17.4453L21.7695 50.4375L26.0938 53H28.6562L32.5 50.4375L36.3438 53H38.9062L43.2305 50.4375L47.5547 53H53L52.3594 51.2383L53 49.3965V47.5547L50.4375 43.2305L53 38.9062V36.3438L50.4375 32.5L53 28.6562V26.0938L50.4375 21.7695L53 17.4453V12L51.2383 12.6406L49.3965 12H47.5547L43.2305 14.5625L38.9062 12H36.3438L32.5 14.5625L28.6562 12H26.0938L21.7695 14.5625L17.4453 12H12Z" fill="#EEEEEE"/>--}}
                    {{--                                            <path d="M38.9062 12V20.6484L47.5547 12H38.9062ZM53 17.4453L44.3516 26.0938H53V17.4453ZM12 26.0938H20.6484L12 17.4453V26.0938ZM17.4453 12L26.0938 20.6484V12H17.4453ZM26.0938 53V44.3516L17.4453 53H26.0938ZM12 47.5547L20.6484 38.9062H12V47.5547ZM53 38.9062H44.3516L53 47.5547V38.9062ZM47.5547 53L38.9062 44.3516V53H47.5547Z" fill="#0052B4"/>--}}
                    {{--                                            <path d="M12 12V15.6035L22.4902 26.0938H26.0938L12 12ZM28.6562 12V28.6562H12V36.3438H28.6562V53H36.3438V36.3438H53V28.6562H36.3438V12H28.6562ZM49.3965 12L38.9062 22.4902V26.0938L53 12H49.3965ZM26.0938 38.9062L12 53H15.6035L26.0938 42.5098V38.9062ZM38.9062 38.9062L53 53V49.3965L42.5098 38.9062H38.9062Z" fill="#D80027"/>--}}
                    {{--                                        </g>--}}
                    {{--                                        <defs>--}}
                    {{--                                            <linearGradient id="paint0_linear_311_437" x1="2289.13" y1="4147.16" x2="2289.13" y2="9.74194" gradientUnits="userSpaceOnUse">--}}
                    {{--                                                <stop stop-color="#1FAF38"/>--}}
                    {{--                                                <stop offset="1" stop-color="#60D669"/>--}}
                    {{--                                            </linearGradient>--}}
                    {{--                                            <linearGradient id="paint1_linear_311_437" x1="2368" y1="4294.78" x2="2368" y2="9" gradientUnits="userSpaceOnUse">--}}
                    {{--                                                <stop stop-color="#F9F9F9"/>--}}
                    {{--                                                <stop offset="1" stop-color="white"/>--}}
                    {{--                                            </linearGradient>--}}
                    {{--                                            <clipPath id="clip0_311_437">--}}
                    {{--                                                <rect width="46" height="43" fill="white" transform="translate(68 9)"/>--}}
                    {{--                                            </clipPath>--}}
                    {{--                                        </defs>--}}
                    {{--                                    </svg>--}}
                    {{--                                    N{{number_format($ukamont, 2)}}--}}
                    {{--                                </button>--}}


                    {{--                            </form>--}}







                    {{--                        </div>--}}
                    {{--                    @endif--}}


                    {{--                    <div class="card">--}}
                    {{--                        <div class="card-body">--}}
                    {{--                            <div class="card-header">--}}
                    {{--                                <h6>Recent Orders</h6>--}}
                    {{--                            </div>--}}
                    {{--                            <div class="table-responsive">--}}
                    {{--                                <table class="table">--}}
                    {{--                                    <thead style="background: rgb(84,84,84); border-radius: 10px; color: white">--}}
                    {{--                                    <tr>--}}
                    {{--                                        <th class="text-white">ID</th>--}}
                    {{--                                        <th class="text-white">Phone</th>--}}
                    {{--                                        <th class="text-white">SMS</th>--}}
                    {{--                                        <th class="text-white">Amount</th>--}}
                    {{--                                        <th class="text-white">Status</th>--}}
                    {{--                                        <th class="text-white">Action</th>--}}

                    {{--                                    </tr>--}}
                    {{--                                    </thead>--}}


                    {{--                                    @foreach ($orders as $data)--}}

                    {{--                                        <tbody>--}}

                    {{--                                        <tr>--}}

                    {{--                                            <td>--}}
                    {{--                                                {{ $data->id }}--}}
                    {{--                                            </td>--}}
                    {{--                                            <td>--}}
                    {{--                                                {{ $data->phone }}--}}
                    {{--                                            </td>--}}
                    {{--                                            <td>--}}
                    {{--                                                {{ $data->sms}}--}}
                    {{--                                            </td>--}}

                    {{--                                            <td>--}}
                    {{--                                                {{ number_format($data->cost, 2) }}--}}
                    {{--                                            </td>--}}

                    {{--                                            @if($data->status == 2)--}}
                    {{--                                                <td class="text-success">--}}
                    {{--                                                    Delivered--}}
                    {{--                                                </td>--}}
                    {{--                                            @else--}}
                    {{--                                                <td class="text-warning">--}}
                    {{--                                                    Pending--}}
                    {{--                                                </td>--}}
                    {{--                                            @endif--}}

                    {{--                                            <td>--}}
                    {{--                                                <a href="delete-order?id={{$data->id}}"--}}
                    {{--                                                   class="btn btn-sm btn-dark text-small">Delete</a>--}}
                    {{--                                            </td>--}}

                    {{--                                        </tr>--}}


                    {{--                                        </tbody>--}}

                    {{--                                    @endforeach--}}

                    {{--                                </table>--}}
                    {{--                            </div>--}}


                    {{--                        </div>--}}

                    {{--                    </div>--}}


                </div>

            </div>


        </div>


    </section>



    <script>
        function filterServices() {
            var input, filter, serviceRows, serviceNames, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            serviceRows = document.getElementsByClassName("service-row");
            for (i = 0; i < serviceRows.length; i++) {
                serviceNames = serviceRows[i].getElementsByClassName("service-name");
                txtValue = serviceNames[0].textContent || serviceNames[0].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    serviceRows[i].style.display = "";
                } else {
                    serviceRows[i].style.display = "none";
                }
            }
        }
    </script>






    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('data-table');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const countdownElement = row.cells[2]; // Assumes "Expires" is in the third column (index 2)
                let seconds = parseInt(countdownElement.getAttribute('data-seconds'), 10);

                const countdownInterval = setInterval(function () {
                    countdownElement.textContent = seconds + 's';

                    if (seconds <= 0) {
                        clearInterval(countdownInterval);
                        // Add your logic to handle the expiration, e.g., sendPostRequest(row);
                        console.log('Expired:', row);
                    }

                    seconds--;
                }, 1000);
            });

            // You may add the sendPostRequest function here or modify the code accordingly
        });
    </script>

    <script>
        $(document).ready(function () {
            //change selectboxes to selectize mode to be searchable
            $("select").select2();
        });
    </script>

@endsection
