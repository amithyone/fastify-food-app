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


                    <div class="col-12 my-5">
                        @auth
                            <div class="col-12">
                                <div class="card" style="background: #064174; color: #ffffff">
                                    <div class="card-body">

                                        <div class="">

                                            <div class="p-2 col-lg-12">
                                                <strong>
                                                    <h4 class="text-white d-flex justify-content-center my-3">
                                                        Verifications</h4>
                                                </strong>
                                            </div>

                                            <div>


                                                <div class="table-responsive ">
                                                    <table class="table">
                                                        <thead style="background: white; border: 0px;">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Service</th>
                                                            <th>Phone No</th>
                                                            <th>Code</th>
                                                            <th>Price</th>
                                                            <th>Status</th>
                                                            <th>Date</th>


                                                        </tr>
                                                        </thead>
                                                        <tbody class="text-white">


                                                        @forelse($verification as $data)
                                                            <tr>
                                                                <td style="font-size: 12px; color: white">{{ $data->id }}</td>
                                                                <td style="font-size: 12px; color: white">{{ $data->service }}</td>
                                                                <td style="font-size: 12px; color: white"><a
                                                                        href="receive-sms?phone={{ $data->id }}">{{ $data->phone }} </a>
                                                                </td>
                                                                <td style="font-size: 12px; color: white">{{ $data->sms }}</td>
                                                                <td style="font-size: 12px; color: white">
                                                                    ₦{{ number_format($data->cost, 2) }}</td>
                                                                <td>
                                                                    @if ($data->status == 1)
                                                                        <span
                                                                            style="background: orange; border:0px; font-size: 10px"
                                                                            class="btn btn-warning btn-sm">Pending</span>
                                                                        <a href="delete-order?id={{  $data->id }}&delete=1"
                                                                           style="background: rgb(168, 0, 14); border:0px; font-size: 10px"
                                                                           class="btn btn-warning btn-sm">Delete</span>

                                                                            @else
                                                                                <span style="font-size: 10px;"
                                                                                      class="text-white btn btn-success btn-sm">Completed</span>
                                                                    @endif

                                                                </td>
                                                                <td style="font-size: 12px; color: white">{{ $data->created_at }}</td>
                                                            </tr>

                                                        @empty

                                                            <h6>No verification found</h6>
                                                        @endforelse

                                                        </tbody>

                                                        {{ $verification->links() }}

                                                    </table>
                                                </div>
                                            </div>


                                        </div>


                                    </div>
                                </div><!-- [ sample-page ] end -->

                                @endauth
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

@endsection
