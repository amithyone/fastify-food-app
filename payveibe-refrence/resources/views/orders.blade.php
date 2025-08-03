@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">
        <div class="container title my-5">
            <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
                <div class="col-md-8 col-xl-6">
                    <h4 class="mb-3" style="color: #fc6507">Hi {{ Auth::user()->username }} 👋</h4>
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
                                                    <form action="server1_order" method="POST">
                                                        @csrf
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



        <div class="container technology-block">
            <div class="col-lg-12 col-sm-12 d-flex justify-content-center">
                <div class="card border-0 mb-5 rounded-20" style="background: #064174; color: #ffffff">
                    <div class="card-body">

                        <div class="card-header d-flex justify-content-center mb-3">
                            <h5 class="text-white">My Orders</h5>
                        </div>


                        <div class="text-center mb-3">
                            <p class="text-warning">Do not refresh page automatically refresh itself</p>
                        </div>


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


                        <div class="col-xl-12 col-md-12 col-sm-12  justify-center">
                            <div class="card" style="background: #064174; color: #ffffff">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead style="background: white; border: 0px;">
                                            <tr>
                                                <th>Service</th>
                                                <th>Phone</th>
                                                <th>SMS</th>
                                                <th>Action</th>

                                            </tr>
                                            </thead>


                                            @forelse($orders as $data)
                                                <tr>
                                                    <td style="font-size: 12px; color: white;">{{ $data->service }}</td>
                                                    <td style="font-size: 12px; color: white;">{{ $data->phone }}
                                                    </td>


                                                    @if($data->type == 4)
                                                        @if($data->sms != null)
                                                            <td style="font-size: 12px; color: #ffffff">{{ $data->sms }}
                                                            </td>
                                                        @else
                                                            <style>
                                                                /* HTML: <div class="loader"></div> */
                                                                .loader {
                                                                    width: 50px;
                                                                    aspect-ratio: 1;
                                                                    display: grid;
                                                                    animation: l14 4s infinite;
                                                                }

                                                                .loader::before,
                                                                .loader::after {
                                                                    content: "";
                                                                    grid-area: 1/1;
                                                                    border: 8px solid;
                                                                    border-radius: 50%;
                                                                    border-color: red red #0000 #0000;
                                                                    mix-blend-mode: darken;
                                                                    animation: l14 1s infinite linear;
                                                                }

                                                                .loader::after {
                                                                    border-color: #0000 #0000 blue blue;
                                                                    animation-direction: reverse;
                                                                }

                                                                @keyframes l14 {
                                                                    100% {
                                                                        transform: rotate(1turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <style>#l1 {
                                                                    width: 15px;
                                                                    aspect-ratio: 1;
                                                                    border-radius: 50%;
                                                                    border: 1px solid;
                                                                    border-color: #fc6507 #0000;
                                                                    animation: l1 1s infinite;
                                                                }

                                                                @keyframes l1 {
                                                                    to {
                                                                        transform: rotate(.5turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <td style="font-size: 12px; color: white">

                                                                <div>
                                                                    <input style="background: none;" class="border-0 "
                                                                           id="response-input{{$data->id}}">
                                                                </div>


                                                                <script>
                                                                    makeRequest{{$data->id}}();
                                                                    setInterval(makeRequest{{$data->id}}, 5000);

                                                                    function makeRequest{{$data->id}}() {
                                                                        fetch('{{ url('') }}/get-viopsms?num={{ $data->phone }}')
                                                                            .then(response => {
                                                                                if (!response.ok) {
                                                                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                                                                }
                                                                                return response.json();
                                                                            })
                                                                            .then(data => {

                                                                                console.log(data.message);
                                                                                displayResponse{{$data->id}}(data.message);

                                                                            })
                                                                            .catch(error => {
                                                                                console.error('Error:', error);
                                                                                displayResponse{{$data->id}}({
                                                                                    error: 'An error occurred while fetching the data.'
                                                                                });
                                                                            });
                                                                    }

                                                                    function displayResponse{{$data->id}}(data) {
                                                                        const responseInput = document.getElementById('response-input{{$data->id}}');
                                                                        responseInput.value = data;
                                                                    }

                                                                </script>
                                                            </td>
                                                        @endif

                                                            <td>
                                                                @if ($data->status == 1)
                                                                    <span
                                                                        style="background: orange; border:0px; font-size: 10px"
                                                                        class="btn btn-warning btn-sm">Pending</span>
                                                                    <a href="cancle-viop?id={{  $data->order_id }}&delete=1"
                                                                       style="background: rgb(168, 0, 14); border:0px; font-size: 10px"
                                                                       onclick="this.style.display='none'"
                                                                       class="btn btn-warning btn-sm">Delete</span>

                                                                        @else
                                                                            <span style="font-size: 10px;"
                                                                                  class="text-white btn btn-success btn-sm">Completed</span>
                                                                @endif

                                                            </td>



                                                    @elseif($data->type == 3)

                                                        @if($data->sms != null)
                                                            <td style="font-size: 12px;">{{ $data->sms }}
                                                            </td>
                                                        @else
                                                            <style>
                                                                /* HTML: <div class="loader"></div> */
                                                                .loader {
                                                                    width: 50px;
                                                                    aspect-ratio: 1;
                                                                    display: grid;
                                                                    animation: l14 4s infinite;
                                                                }

                                                                .loader::before,
                                                                .loader::after {
                                                                    content: "";
                                                                    grid-area: 1/1;
                                                                    border: 8px solid;
                                                                    border-radius: 50%;
                                                                    border-color: red red #0000 #0000;
                                                                    mix-blend-mode: darken;
                                                                    animation: l14 1s infinite linear;
                                                                }

                                                                .loader::after {
                                                                    border-color: #0000 #0000 blue blue;
                                                                    animation-direction: reverse;
                                                                }

                                                                @keyframes l14 {
                                                                    100% {
                                                                        transform: rotate(1turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <style>#l1 {
                                                                    width: 15px;
                                                                    aspect-ratio: 1;
                                                                    border-radius: 50%;
                                                                    border: 1px solid;
                                                                    border-color: #000 #0000;
                                                                    animation: l1 1s infinite;
                                                                }

                                                                @keyframes l1 {
                                                                    to {
                                                                        transform: rotate(.5turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <td>
                                                                <div class="justify-content-start">
                                                                </div>
                                                                <div>
                                                                    <input class="border-0 justify-content-end"
                                                                           id="response-input{{$data->id}}">
                                                                </div>


                                                                <script>
                                                                    makeRequest{{$data->id}}();
                                                                    setInterval(makeRequest{{$data->id}}, 10000);

                                                                    function makeRequest{{$data->id}}() {
                                                                        fetch('{{ url('') }}/get-csms?id={{ $data->order_id }}')
                                                                            .then(response => {
                                                                                if (!response.ok) {
                                                                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                                                                }
                                                                                return response.json();
                                                                            })
                                                                            .then(data => {

                                                                                console.log(data.message);
                                                                                displayResponse{{$data->id}}(data.message);

                                                                            })
                                                                            .catch(error => {
                                                                                console.error('Error:', error);
                                                                                displayResponse{{$data->id}}({
                                                                                    error: 'An error occurred while fetching the data.'
                                                                                });
                                                                            });
                                                                    }

                                                                    function displayResponse{{$data->id}}(data) {
                                                                        const responseInput = document.getElementById('response-input{{$data->id}}');
                                                                        responseInput.value = data;
                                                                    }

                                                                </script>
                                                            </td>
                                                        @endif

                                                            <td>
                                                                @if ($data->status == 1)
                                                                    <span
                                                                        style="background: orange; border:0px; font-size: 10px"
                                                                        class="btn btn-warning btn-sm">Pending</span>
                                                                    <a href="c-sms?id={{  $data->id }}&delete=1"
                                                                       style="background: rgb(168, 0, 14); border:0px; font-size: 10px"
                                                                       onclick="this.style.display='none'"
                                                                       class="btn btn-warning btn-sm">Delete</span>

                                                                        @else
                                                                            <span style="font-size: 10px;"
                                                                                  class="text-white btn btn-success btn-sm">Completed</span>
                                                                @endif

                                                            </td>

                                                    @elseif($data->type == 2)

                                                        @if($data->sms != null)
                                                            <td style="font-size: 12px; color: #ffffff">{{ $data->sms }}
                                                            </td>
                                                        @else
                                                            <style>
                                                                /* HTML: <div class="loader"></div> */
                                                                .loader {
                                                                    width: 50px;
                                                                    aspect-ratio: 1;
                                                                    display: grid;
                                                                    animation: l14 4s infinite;
                                                                }

                                                                .loader::before,
                                                                .loader::after {
                                                                    content: "";
                                                                    grid-area: 1/1;
                                                                    border: 8px solid;
                                                                    border-radius: 50%;
                                                                    border-color: red red #0000 #0000;
                                                                    mix-blend-mode: darken;
                                                                    animation: l14 1s infinite linear;
                                                                }

                                                                .loader::after {
                                                                    border-color: #0000 #0000 blue blue;
                                                                    animation-direction: reverse;
                                                                }

                                                                @keyframes l14 {
                                                                    100% {
                                                                        transform: rotate(1turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <style>#l1 {
                                                                    width: 15px;
                                                                    aspect-ratio: 1;
                                                                    border-radius: 50%;
                                                                    border: 1px solid;
                                                                    border-color: #fc6507 #0000;
                                                                    animation: l1 1s infinite;
                                                                }

                                                                @keyframes l1 {
                                                                    to {
                                                                        transform: rotate(.5turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <td style="font-size: 12px; color: white">
                                                                <div id="l1" class="justify-content-start">
                                                                </div>
                                                                <div>
                                                                    <input style="background: none;" class="border-0 "
                                                                           id="response-input{{$data->id}}">
                                                                </div>


                                                                <script>
                                                                    makeRequest{{$data->id}}();
                                                                    setInterval(makeRequest{{$data->id}}, 5000);

                                                                    function makeRequest{{$data->id}}() {
                                                                        fetch('{{ url('') }}/get-smscode?num={{ $data->phone }}')
                                                                            .then(response => {
                                                                                if (!response.ok) {
                                                                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                                                                }
                                                                                return response.json();
                                                                            })
                                                                            .then(data => {

                                                                                console.log(data.message);
                                                                                displayResponse{{$data->id}}(data.message);

                                                                            })
                                                                            .catch(error => {
                                                                                console.error('Error:', error);
                                                                                displayResponse{{$data->id}}({
                                                                                    error: 'An error occurred while fetching the data.'
                                                                                });
                                                                            });
                                                                    }

                                                                    function displayResponse{{$data->id}}(data) {
                                                                        const responseInput = document.getElementById('response-input{{$data->id}}');
                                                                        responseInput.value = data;
                                                                    }

                                                                </script>
                                                            </td>


                                                        @endif

                                                            <td>
                                                                @if ($data->status == 1)
                                                                    <span
                                                                        style="background: orange; border:0px; font-size: 10px"
                                                                        class="btn btn-warning btn-sm">Pending</span>
                                                                    <a href="cancleworld-sms?id={{  $data->id }}&delete=1"
                                                                       style="background: rgb(168, 0, 14); border:0px; font-size: 10px"  onclick="this.style.display='none'"
                                                                       class="btn btn-warning btn-sm">Delete</span>

                                                                        @else
                                                                            <span style="font-size: 10px;"
                                                                                  class="text-white btn btn-success btn-sm">Completed</span>
                                                                @endif

                                                            </td>

                                                    @elseif($data->type == 1)
                                                        @if($data->sms != null)
                                                            <td style="font-size: 12px; color: #ffffff">{{ $data->sms }}
                                                            </td>
                                                        @else
                                                            <style>
                                                                /* HTML: <div class="loader"></div> */
                                                                .loader {
                                                                    width: 50px;
                                                                    aspect-ratio: 1;
                                                                    display: grid;
                                                                    animation: l14 4s infinite;
                                                                }

                                                                .loader::before,
                                                                .loader::after {
                                                                    content: "";
                                                                    grid-area: 1/1;
                                                                    border: 8px solid;
                                                                    border-radius: 50%;
                                                                    border-color: red red #0000 #0000;
                                                                    mix-blend-mode: darken;
                                                                    animation: l14 1s infinite linear;
                                                                }

                                                                .loader::after {
                                                                    border-color: #0000 #0000 blue blue;
                                                                    animation-direction: reverse;
                                                                }

                                                                @keyframes l14 {
                                                                    100% {
                                                                        transform: rotate(1turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <style>#l1 {
                                                                    width: 15px;
                                                                    aspect-ratio: 1;
                                                                    border-radius: 50%;
                                                                    border: 1px solid;
                                                                    border-color: #fc6507 #0000;
                                                                    animation: l1 1s infinite;
                                                                }

                                                                @keyframes l1 {
                                                                    to {
                                                                        transform: rotate(.5turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <td style="font-size: 12px; color: white">

                                                                <div>
                                                                    <input style="background: none;" class="border-0 "
                                                                           id="response-input{{$data->id}}">
                                                                </div>


                                                                <script>
                                                                    makeRequest{{$data->id}}();
                                                                    setInterval(makeRequest{{$data->id}}, 5000);

                                                                    function makeRequest{{$data->id}}() {
                                                                        fetch('{{ url('') }}/get-smscode?num={{ $data->phone }}')
                                                                            .then(response => {
                                                                                if (!response.ok) {
                                                                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                                                                }
                                                                                return response.json();
                                                                            })
                                                                            .then(data => {

                                                                                console.log(data.message);
                                                                                displayResponse{{$data->id}}(data.message);

                                                                            })
                                                                            .catch(error => {
                                                                                console.error('Error:', error);
                                                                                displayResponse{{$data->id}}({
                                                                                    error: 'An error occurred while fetching the data.'
                                                                                });
                                                                            });
                                                                    }

                                                                    function displayResponse{{$data->id}}(data) {
                                                                        const responseInput = document.getElementById('response-input{{$data->id}}');
                                                                        responseInput.value = data;
                                                                    }

                                                                </script>
                                                            </td>
                                                        @endif
                                                    @else
                                                        @if($data->sms != null)
                                                            <td style="font-size: 12px; color: #ffffff">{{ $data->sms }}
                                                            </td>
                                                        @else
                                                            <style>
                                                                /* HTML: <div class="loader"></div> */
                                                                .loader {
                                                                    width: 50px;
                                                                    aspect-ratio: 1;
                                                                    display: grid;
                                                                    animation: l14 4s infinite;
                                                                }

                                                                .loader::before,
                                                                .loader::after {
                                                                    content: "";
                                                                    grid-area: 1/1;
                                                                    border: 8px solid;
                                                                    border-radius: 50%;
                                                                    border-color: red red #0000 #0000;
                                                                    mix-blend-mode: darken;
                                                                    animation: l14 1s infinite linear;
                                                                }

                                                                .loader::after {
                                                                    border-color: #0000 #0000 blue blue;
                                                                    animation-direction: reverse;
                                                                }

                                                                @keyframes l14 {
                                                                    100% {
                                                                        transform: rotate(1turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <style>#l1 {
                                                                    width: 15px;
                                                                    aspect-ratio: 1;
                                                                    border-radius: 50%;
                                                                    border: 1px solid;
                                                                    border-color: #fc6507 #0000;
                                                                    animation: l1 1s infinite;
                                                                }

                                                                @keyframes l1 {
                                                                    to {
                                                                        transform: rotate(.5turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <td style="font-size: 12px; color: white">
                                                                <div id="l1" class="justify-content-start">
                                                                </div>
                                                                <div>
                                                                    <input style="background: none;" class="border-0 "
                                                                           id="response-input{{$data->id}}">
                                                                </div>


                                                                <script>
                                                                    makeRequest{{$data->id}}();
                                                                    setInterval(makeRequest{{$data->id}}, 5000);

                                                                    function makeRequest{{$data->id}}() {
                                                                        fetch('{{ url('') }}/get-smscode?num={{ $data->phone }}')
                                                                            .then(response => {
                                                                                if (!response.ok) {
                                                                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                                                                }
                                                                                return response.json();
                                                                            })
                                                                            .then(data => {

                                                                                console.log(data.message);
                                                                                displayResponse{{$data->id}}(data.message);

                                                                            })
                                                                            .catch(error => {
                                                                                console.error('Error:', error);
                                                                                displayResponse{{$data->id}}({
                                                                                    error: 'An error occurred while fetching the data.'
                                                                                });
                                                                            });
                                                                    }

                                                                    function displayResponse{{$data->id}}(data) {
                                                                        const responseInput = document.getElementById('response-input{{$data->id}}');
                                                                        responseInput.value = data;
                                                                    }

                                                                </script>
                                                            </td>
                                                        @endif
                                                    @endif

                                                    <td>
                                                        @if ($data->status == 1)
                                                            <span
                                                                style="background: orange; border:0px; font-size: 10px"
                                                                class="btn btn-warning btn-sm">Pending</span>
                                                            <a href="delete-order?id={{  $data->id }}&delete=1"
                                                               style="background: rgb(168, 0, 14); border:0px; font-size: 10px"  onclick="this.style.display='none'"
                                                               class="btn btn-warning btn-sm">Delete</span>

                                                                @else
                                                                    <span style="font-size: 10px;"
                                                                          class="text-white btn btn-success btn-sm">Completed</span>
                                                        @endif

                                                    </td>

                                                </tr>

                                            @empty

                                                <p class="text-white mb-2">No verification found</p>
                                            @endforelse

                                        </table>
                                    </div>


                                </div>

                            </div>

                        </div>

                    </div>
                </div>


            </div>
        </div>

        <script>
            window.onload = function () {
                setInterval(function () {
                    location.reload();
                }, 10000);
            };
        </script>


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


    </section>

@endsection
