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


                            @foreach ($servicesd as $key => $value)
                                <div class="row service-row text-white">
                                    @foreach ($value as $innerKey => $innerValue)
                                        <div style="font-size: 11px"
                                             class="col-5 service-name d-flex justify-content-start">
                                            🇺🇸 {{ $innerValue->name }}
                                        </div>

                                        <div style="font-size: 11px" class="col">
                                            @php $costd = $get_rated * $innerValue->cost + $margind  @endphp
                                            <strong>N{{ number_format($costd, 2) }}</strong>
                                        </div>

                                        <div style="font-size: 11px" class="col">

                                        </div>


                                        <div class="col mr-3">
                                            @auth
                                                <form action="order-usano" method="POST">
                                                    <input hidden name="service"
                                                           value="{{ $key }}">
                                                    <input hidden name="price"
                                                           value="{{ $costd }}">
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
                    <div class="mt-2">

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


                    </div>

                    <h4 class="mb-3" style="color: #fc6507">Hi {{ Auth::user()->username }} 👋</h4>
                    <p class="mb-0">
                        <a href="fund-wallet"
                           class="btn btn-dark mb-4">NGN {{ number_format(Auth::user()->wallet, 2) }}</a>
                    </p>
                    <p class="mb-2">
                        What will you like to do ?
                    </p>



                    @include('servermenu')


                    <div class="col-xl-12 col-md-12 col-sm-12  my-5 justify-center">
                        <div class="card" style="background: #3861ff; color: #ffffff">
                            <div class="card-body">


                                <div class="form-group ">
                                    <h4 class="text-white my-4"> Search for 🇺🇸 USA services only</h4>
                                        <p class="text-white text-center mb-2">Verify all USA services</p>
                                    <input type="text" class="form-control" id="searchMeter"
                                           placeholder="Search for services ex: Facebook, whatsapp, Telegram">

                                    <div id="serviceResult" class="my-5" style="display: none;"></div>
                                </div>

                                <script>
                                    document.getElementById('searchMeter').addEventListener('keyup', function () {
                                        let query = this.value;
                                        console.log('User input:', query);

                                        if (query.length > 2) {
                                            let xhr = new XMLHttpRequest();
                                            xhr.open('GET', '/search-viop-services?q=' + query, true);

                                            xhr.onreadystatechange = function () {
                                                if (xhr.readyState == 4 && xhr.status == 200) {
                                                    let services = JSON.parse(xhr.responseText);
                                                    let serviceResultDiv = document.getElementById('serviceResult');
                                                    serviceResultDiv.innerHTML = ''; // Clear previous results

                                                    if (services.length > 0) {
                                                        services.forEach(service => {
                                                            // Calculate cost
                                                            let get_rate = {{ $get_rate }};
                                                            let margin = {{ $margin }};
                                                            let cost = get_rate * service.price + margin;

                                                            // Create div for each service
                                                            let div = document.createElement('div');

                                                            let formatter = new Intl.NumberFormat('en-NG', {
                                                                style: 'currency',
                                                                currency: 'NGN',
                                                                minimumFractionDigits: 2,
                                                            });
                                                            let formattedCost = formatter.format(cost);


                                                            div.className = 'row service-row';

                                                            div.innerHTML = `
<div class="card">
<div class="card-body">

<div class="row">


                                <div class="d-flex justify-content-center mb-3"> <? xml version = "1.0" encoding = "utf-8" ?><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="50px" width="50" viewBox="0 0 55.2 38.4" style="enable-background:new 0 0 55.2 38.4" xml:space="preserve"><style type="text/css">.st0{fill:#B22234;} .st1{fill:#FFFFFF;} .st2{fill:#3C3B6E;}</style><g><path class="st0" d="M3.03,0h49.13c1.67,0,3.03,1.36,3.03,3.03v32.33c0,1.67-1.36,3.03-3.03,3.03H3.03C1.36,38.4,0,37.04,0,35.37 V3.03C0,1.36,1.36,0,3.03,0L3.03,0z"/><path class="st1" d="M0.02,2.73h55.17c0.01,0.1,0.02,0.2,0.02,0.31v2.94H0V3.03C0,2.93,0.01,2.83,0.02,2.73L0.02,2.73z M55.2,8.67 v3.24H0V8.67H55.2L55.2,8.67z M55.2,14.61v3.24H0v-3.24H55.2L55.2,14.61z M55.2,20.55v3.24H0v-3.24H55.2L55.2,20.55z M55.2,26.49 v3.24H0v-3.24H55.2L55.2,26.49z M55.2,32.43v2.93c0,0.1-0.01,0.21-0.02,0.31H0.02C0.01,35.58,0,35.47,0,35.37v-2.93H55.2 L55.2,32.43z"/><path class="st2" d="M20.8,0v20.68H0V3.03C0,1.36,1.36,0,3.03,0H20.8L20.8,0L20.8,0z"/><polygon class="st1" points="1.23,2.86 1.92,5.01 0.1,3.68 2.36,3.68 0.53,5.01 1.23,2.86"/><polygon class="st1" points="1.23,7.02 1.92,9.17 0.1,7.84 2.36,7.84 0.53,9.17 1.23,7.02"/><polygon class="st1" points="1.23,11.18 1.92,13.33 0.1,12 2.36,12 0.53,13.33 1.23,11.18"/><polygon class="st1" points="1.23,15.34 1.92,17.49 0.1,16.16 2.36,16.16 0.53,17.49 1.23,15.34"/><polygon class="st1" points="3.67,0.78 4.37,2.93 2.54,1.6 4.81,1.6 2.97,2.93 3.67,0.78"/><polygon class="st1" points="3.67,4.94 4.37,7.09 2.54,5.76 4.81,5.76 2.97,7.09 3.67,4.94"/><polygon class="st1" points="3.67,9.1 4.37,11.25 2.54,9.92 4.81,9.92 2.97,11.25 3.67,9.1"/><polygon class="st1" points="3.67,13.26 4.37,15.41 2.54,14.08 4.81,14.08 2.97,15.41 3.67,13.26"/><polygon class="st1" points="3.67,17.42 4.37,19.57 2.54,18.24 4.81,18.24 2.97,19.57 3.67,17.42"/><polygon class="st1" points="6.12,2.86 6.82,5.01 4.99,3.68 7.25,3.68 5.42,5.01 6.12,2.86"/><polygon class="st1" points="6.12,7.02 6.82,9.17 4.99,7.84 7.25,7.84 5.42,9.17 6.12,7.02"/><polygon class="st1" points="6.12,11.18 6.82,13.33 4.99,12 7.25,12 5.42,13.33 6.12,11.18"/><polygon class="st1" points="6.12,15.34 6.82,17.49 4.99,16.16 7.25,16.16 5.42,17.49 6.12,15.34"/><polygon class="st1" points="8.57,0.78 9.26,2.93 7.44,1.6 9.7,1.6 7.87,2.93 8.57,0.78"/><polygon class="st1" points="8.57,4.94 9.26,7.09 7.44,5.76 9.7,5.76 7.87,7.09 8.57,4.94"/><polygon class="st1" points="8.57,9.1 9.26,11.25 7.44,9.92 9.7,9.92 7.87,11.25 8.57,9.1"/><polygon class="st1" points="8.57,13.26 9.26,15.41 7.44,14.08 9.7,14.08 7.87,15.41 8.57,13.26"/><polygon class="st1" points="8.57,17.42 9.26,19.57 7.44,18.24 9.7,18.24 7.87,19.57 8.57,17.42"/><polygon class="st1" points="11.01,2.86 11.71,5.01 9.88,3.68 12.14,3.68 10.31,5.01 11.01,2.86"/><polygon class="st1" points="11.01,7.02 11.71,9.17 9.88,7.84 12.14,7.84 10.31,9.17 11.01,7.02"/><polygon class="st1" points="11.01,11.18 11.71,13.33 9.88,12 12.14,12 10.31,13.33 11.01,11.18"/><polygon class="st1" points="11.01,15.34 11.71,17.49 9.88,16.16 12.14,16.16 10.31,17.49 11.01,15.34"/><polygon class="st1" points="13.46,0.78 14.16,2.93 12.33,1.6 14.59,1.6 12.76,2.93 13.46,0.78"/><polygon class="st1" points="13.46,4.94 14.16,7.09 12.33,5.76 14.59,5.76 12.76,7.09 13.46,4.94"/><polygon class="st1" points="13.46,9.1 14.16,11.25 12.33,9.92 14.59,9.92 12.76,11.25 13.46,9.1"/><polygon class="st1" points="13.46,13.26 14.16,15.41 12.33,14.08 14.59,14.08 12.76,15.41 13.46,13.26"/><polygon class="st1" points="13.46,17.42 14.16,19.57 12.33,18.24 14.59,18.24 12.76,19.57 13.46,17.42"/><polygon class="st1" points="15.9,2.86 16.6,5.01 14.77,3.68 17.03,3.68 15.21,5.01 15.9,2.86"/><polygon class="st1" points="15.9,7.02 16.6,9.17 14.77,7.84 17.03,7.84 15.21,9.17 15.9,7.02"/><polygon class="st1" points="15.9,11.18 16.6,13.33 14.77,12 17.03,12 15.21,13.33 15.9,11.18"/><polygon class="st1" points="15.9,15.34 16.6,17.49 14.77,16.16 17.03,16.16 15.21,17.49 15.9,15.34"/><polygon class="st1" points="18.35,0.78 19.05,2.93 17.22,1.6 19.48,1.6 17.65,2.93 18.35,0.78"/><polygon class="st1" points="18.35,4.94 19.05,7.09 17.22,5.76 19.48,5.76 17.65,7.09 18.35,4.94"/><polygon class="st1" points="18.35,9.1 19.05,11.25 17.22,9.92 19.48,9.92 17.65,11.25 18.35,9.1"/><polygon class="st1" points="18.35,13.26 19.05,15.41 17.22,14.08 19.48,14.08 17.65,15.41 18.35,13.26"/><polygon class="st1" points="18.35,17.42 19.05,19.57 17.22,18.24 19.48,18.24 17.65,19.57 18.35,17.42"/></g></svg> </div>


</div>

<div class="row">


                                <div class="col-5 mt-2 mb-3 service-name">${service.name}</div>
                                <div class="col mt-2 ">
                                    <strong>${formattedCost}</strong>
                                </div>

</div>
<div class="row">
                                <div class="col-5 mt-1 mb-3">
                                    Available: ${service.available}
                                </div>


                                <div class="col mt-1">
                                    <button  onclick="this.disabled=true; buyService(${cost}, ${service.product_id})" class="myButton" style="border: 0px; font-size:20px; color: #3861ff; background: transparent"  onclick="buyService(${cost}, ${service.product_id})">
                                        <i class="fa fa-shopping-bag"></i>
                                    </button>


                                </div>
</div>
</div>
</div>
</div>
                            `;


                                                            // Append each service to the result div
                                                            serviceResultDiv.appendChild(div);
                                                        });

                                                        serviceResultDiv.style.display = 'block'; // Show results
                                                    } else {
                                                        let noResultDiv = document.createElement('div');
                                                        noResultDiv.textContent = 'No services found';
                                                        noResultDiv.style.color = 'red';
                                                        serviceResultDiv.appendChild(noResultDiv);
                                                        serviceResultDiv.style.display = 'block'; // Show "No services found" message
                                                    }
                                                } else if (xhr.readyState == 4) {
                                                    console.log('Error: Status', xhr.status);
                                                }
                                            };

                                            xhr.onerror = function () {
                                                console.error('Request error');
                                            };

                                            xhr.send();
                                        } else {
                                            document.getElementById('serviceResult').style.display = 'none'; // Hide if input is too short
                                        }
                                    });

                                    // Function to handle the purchase of a service
                                    function buyService(cost, productId) {
                                        let xhr = new XMLHttpRequest();
                                        xhr.open('POST', '/viop-buy', true);
                                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                                        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                                        xhr.setRequestHeader('X-CSRF-TOKEN', token);

                                        let button = event.target;
                                        button.disabled = true;

                                        xhr.send(`cost=${cost}&product_id=${productId}`);

                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState == 4 && xhr.status == 200) {
                                                let jsonResponse = JSON.parse(xhr.responseText);
                                                console.log('Purchase successful:', jsonResponse);

                                                if (jsonResponse.message === "Not Availabe") {
                                                    alert('Verification not available');
                                                } else if (jsonResponse.message === "Insufficient funds") {
                                                    alert('Insufficient funds. Redirecting to fund wallet...');
                                                    window.location.href = '/fund-wallet';
                                                } else if (jsonResponse.message === "Verification successful") {
                                                    window.location.href = '/orders';
                                                }

                                                setTimeout(function () {
                                                    button.disabled = false;
                                                }, 5000);

                                            } else if (xhr.readyState == 4 && xhr.status == 204) {
                                                console.error('Error:', xhr.status);
                                                alert('Verification not available');
                                                button.disabled = false;

                                            } else if (xhr.readyState == 4) {
                                                console.error('Unexpected error:', xhr.status);
                                                button.disabled = false;
                                            }
                                        };
                                    }
                                </script>


                            </div>

                        </div>

                    </div>


                    <div
                        class="offcanvas offcanvas-bottom"
                        tabindex="-1"
                        id="offcanvasBottom"
                        aria-labelledby="offcanvasBottomLabel"
                    >
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasBottomLabel">
                                Offcanvas bottom
                            </h5>
                            <button
                                type="button"
                                class="btn-close text-reset"
                                data-bs-dismiss="offcanvas"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="offcanvas-body py-0">


                        </div>

                    </div>


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


    <script>
        function hideButton(link) {
            // Hide the clicked link
            link.style.display = 'none';

            setTimeout(function () {
                link.style.display = 'inline'; // or 'block' depending on your layout
            }, 5000); // 5 seconds
        }
    </script>

@endsection
