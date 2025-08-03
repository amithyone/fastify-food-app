<!doctype html>
<html lang="en"><!-- [Head] start -->
<head><title>FADDEDSMS</title><!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
          content="FADDEDSMS NUMBER VERIFICATION">
    <meta name="keywords"
          content="FADDEDSMS VERIFICATION">
    <meta name="author" content="Phoenixcoded"><!-- [Favicon] icon -->
    <link rel="icon" href="{{url('')}}/public/assets/images/favicon.svg" type="image/x-icon"><!-- [Font] Family -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/inter/inter.css" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/material.css"><!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="{{url('')}}/public/assets/css/style2.css">
    <link rel="stylesheet" href="{{url('')}}/public/assets/css/style2.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <link rel="stylesheet" type="text/css" href="{{url('')}}/public/api/daisysms.css">

    <script src="{{url('')}}/public/api/l.js" async=""></script>
    <script src="{{url('')}}/public/api/client.js" type="text/javascript" async=""></script>
    <link href="{{url('')}}/public/api/client_default.css" type="text/css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{url('')}}/public/assets/css/style-preset.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
          integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
          crossorigin="anonymous"/>


    <style>
        .search-results {
            max-height: 300px;
            overflow-y: auto;
            position: absolute;
            width: 100%;
            background: #fff;
            border: 1px solid #ddd;
        }

        .search-results li {
            padding: 10px;
            cursor: pointer;
        }

        .search-results li:hover {
            background: #eee;
        }


        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }


        .search-container {
            position: relative;
            display: inline-block;
        }


    </style>


</head><!-- [Head] end --><!-- [Body] Start -->

<header
    id="home"
    style="background-image: url({{url('')}}/public/assets/images/landing/img-headerbg.jpg)"
>
    <!-- [ Nav ] start --><!-- [ Nav ] start -->
    <nav class="navbar navbar-expand-md navbar-light default">
        <div class="container">
            <a class="navbar-brand" href="/"
            ><img src="{{url('')}}/public/assets/images/logo-dark.svg" alt="logo"/> </a>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">

                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="us">Home
                        </a>
                    </li>


                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="fund-wallet">Fund Wallet</a>
                    </li>

                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="orders">My Orders</a>
                    </li>

                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="https://t.me/faddedsms">Support</a>
                    </li>


                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="https://t.me/faddedsms">Join our Telegram channel</a>
                    </li>


                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="https://youtu.be/j3TYFIT1wB4?si=gtWMfhdKcje070T0">Tutorial - How to buy sms
                        </a>
                    </li>



                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="https://fadded-socials.com/"> Buy Social Account</a>
                    </li>


                    <li class="nav-item px-1">
                        <a class="nav-link text-danger" href="log-out">Log Out</a>
                    </li>


                </ul>
            </div>


            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">

                    @auth
                        <li class="nav-item">
                            <a
                                style="background: rgb(63,63,63); color: white"
                                class="btn btn btn-buy"
                                target="_blank"
                                href="fund-wallet"><i class="ti ti-wallet"></i
                                >NGN {{number_format(Auth::user()->wallet, 2)}} </a>
                        </li>
                    @endauth
                </ul>
            </div>


            <button
                style="background: rgb(63,63,63); color: white"
                class="navbar-toggler rounded"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarTogglerDemo01"
                aria-controls="navbarTogglerDemo01"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>


        </div>
    </nav>
    <!-- [ Nav ] start --><!-- [ Nav ] start -->

</header>


<div class="page-loader">
    <div class="bar"></div>
</div>

<body>


@yield('content')


<div class="floating-chat">
    <i class="fa fa-comments" aria-hidden="true"></i>
    <div class="chat">
        <div class="header">
            <span class="title">
                You need support?
            </span>

        </div>
        <ul class="messages">
            <li style="color: white" class="other"><a style="color: white"  href="https://t.me/faddedsms" target="_blank"><i class="bi bi-telegram"> </i> Get Support </a></li>
            <li style="color: white" class="other"><a style="color: white"  href="https://t.me/faddedsms" target="_blank"><i class="bi bi-telegram"> </i> Join our channel</a></li>
        </ul>
    </div>
</div>





<footer class="footer d-flex justify-content-center mb-5">
    <p class="text-center mb-5">2024 FADDEDSMS</p>
</footer>


<!-- Required Js -->
<script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="{{url('')}}/public/assets/js/plugins/popper.min.js"></script>
<script src="{{url('')}}/public/assets/js/plugins/simplebar.min.js"></script>
<script src="{{url('')}}/public/assets/js/plugins/bootstrap.min.js"></script>
<script src="{{url('')}}/public/assets/js/fonts/custom-font.js"></script>
<script src="{{url('')}}/public/assets/js/pcoded.js"></script>
<script src="{{url('')}}/public/assets/js/plugins/feather.min.js"></script>
<script>layout_change('false');</script>
<script>layout_theme_contrast_change('false');</script>
<script>change_box_container('false');</script>
<script>layout_caption_change('true');</script>
<script>layout_rtl_change('false');</script>
<script>preset_change('preset-4');</script>
<script>main_layout_change('vertical');</script>

<script>
    var element = $('.floating-chat');
    var myStorage = localStorage;

    if (!myStorage.getItem('chatID')) {
        myStorage.setItem('chatID', createUUID());
    }

    setTimeout(function () {
        element.addClass('enter');
    }, 1000);

    element.click(openElement);

    function openElement() {
        var messages = element.find('.messages');
        var textInput = element.find('.text-box');
        element.find('>i').hide();
        element.addClass('expand');
        element.find('.chat').addClass('enter');
        var strLength = textInput.val().length * 2;
        textInput.keydown(onMetaAndEnter).prop("disabled", false).focus();
        element.off('click', openElement);
        element.find('.header button').click(closeElement);
        element.find('#sendMessage').click(sendNewMessage);
        messages.scrollTop(messages.prop("scrollHeight"));
    }

    function closeElement() {
        element.find('.chat').removeClass('enter').hide();
        element.find('>i').show();
        element.removeClass('expand');
        element.find('.header button').off('click', closeElement);
        element.find('#sendMessage').off('click', sendNewMessage);
        element.find('.text-box').off('keydown', onMetaAndEnter).prop("disabled", true).blur();
        setTimeout(function () {
            element.find('.chat').removeClass('enter').show()
            element.click(openElement);
        }, 500);
    }

    function createUUID() {
        // http://www.ietf.org/rfc/rfc4122.txt
        var s = [];
        var hexDigits = "0123456789abcdef";
        for (var i = 0; i < 36; i++) {
            s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
        }
        s[14] = "4"; // bits 12-15 of the time_hi_and_version field to 0010
        s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1); // bits 6-7 of the clock_seq_hi_and_reserved to 01
        s[8] = s[13] = s[18] = s[23] = "-";

        var uuid = s.join("");
        return uuid;
    }

    function sendNewMessage() {
        var userInput = $('.text-box');
        var newMessage = userInput.html().replace(/\<div\>|\<br.*?\>/ig, '\n').replace(/\<\/div\>/g, '').trim().replace(/\n/g, '<br>');

        if (!newMessage) return;

        var messagesContainer = $('.messages');

        messagesContainer.append([
            '<li class="self">',
            newMessage,
            '</li>'
        ].join(''));

        // clean out old message
        userInput.html('');
        // focus on input
        userInput.focus();

        messagesContainer.finish().animate({
            scrollTop: messagesContainer.prop("scrollHeight")
        }, 250);
    }

    function onMetaAndEnter(event) {
        if ((event.metaKey || event.ctrlKey) && event.keyCode == 13) {
            sendNewMessage();
        }
    }
</script>


<script>
    // Toggle for country dropdown
    function toggleDropdown() {
        document.getElementById('dropdown').style.display = 'block';
    }

    // Toggle for service dropdown
    function toggleDropdownservice() {
        document.getElementById('dropdownservice').style.display = 'block';
    }

    // Filter for country items
    function filterItems() {
        const searchInput = document.getElementById('search').value.toLowerCase();
        const items = document.querySelectorAll('#dropdown .item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchInput)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Filter for service items
    function filterItemsservice() {
        const searchInput = document.getElementById('searchservice').value.toLowerCase();
        const items = document.querySelectorAll('#dropdownservice .item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchInput)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Select country
    function selectCountry(element) {
        document.getElementById('search').value = element.textContent;
        document.getElementById('dropdown').style.display = 'none';
        document.getElementById('selectedID').value = element.getAttribute('data-id');
    }

    // Select service
    function selectService(element) {
        document.getElementById('searchservice').value = element.textContent;
        document.getElementById('dropdownservice').style.display = 'none';
        document.getElementById('serviceID').value = element.getAttribute('data-id');
    }

    // Close dropdowns if clicked outside
    document.addEventListener('click', function (event) {
        const searchContainerCountry = document.querySelector('#search');
        const searchContainerService = document.querySelector('#searchservice');

        if (!searchContainerCountry.contains(event.target)) {
            document.getElementById('dropdown').style.display = 'none';
        }

        if (!searchContainerService.contains(event.target)) {
            document.getElementById('dropdownservice').style.display = 'none';
        }
    });
</script>


<script>function changebrand(presetColor) {
        removeClassByPrefix(document.querySelector('body'), 'preset-');
        document.querySelector('body').classList.add(presetColor);
    }

    localStorage.setItem('layout', 'color-header');
</script>



<script>
    var element = $('.floating-chat');
    var myStorage = localStorage;

    if (!myStorage.getItem('chatID')) {
        myStorage.setItem('chatID', createUUID());
    }

    setTimeout(function() {
        element.addClass('enter');
    }, 1000);

    element.click(openElement);

    function openElement() {
        var messages = element.find('.messages');
        var textInput = element.find('.text-box');
        element.find('>i').hide();
        element.addClass('expand');
        element.find('.chat').addClass('enter');
        var strLength = textInput.val().length * 2;
        textInput.keydown(onMetaAndEnter).prop("disabled", false).focus();
        element.off('click', openElement);
        element.find('.header button').click(closeElement);
        element.find('#sendMessage').click(sendNewMessage);
        messages.scrollTop(messages.prop("scrollHeight"));
    }

    function closeElement() {
        element.find('.chat').removeClass('enter').hide();
        element.find('>i').show();
        element.removeClass('expand');
        element.find('.header button').off('click', closeElement);
        element.find('#sendMessage').off('click', sendNewMessage);
        element.find('.text-box').off('keydown', onMetaAndEnter).prop("disabled", true).blur();
        setTimeout(function() {
            element.find('.chat').removeClass('enter').show()
            element.click(openElement);
        }, 500);
    }

    function createUUID() {
        // http://www.ietf.org/rfc/rfc4122.txt
        var s = [];
        var hexDigits = "0123456789abcdef";
        for (var i = 0; i < 36; i++) {
            s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
        }
        s[14] = "4"; // bits 12-15 of the time_hi_and_version field to 0010
        s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1); // bits 6-7 of the clock_seq_hi_and_reserved to 01
        s[8] = s[13] = s[18] = s[23] = "-";

        var uuid = s.join("");
        return uuid;
    }

    function sendNewMessage() {
        var userInput = $('.text-box');
        var newMessage = userInput.html().replace(/\<div\>|\<br.*?\>/ig, '\n').replace(/\<\/div\>/g, '').trim().replace(/\n/g, '<br>');

        if (!newMessage) return;

        var messagesContainer = $('.messages');

        messagesContainer.append([
            '<li class="self">',
            newMessage,
            '</li>'
        ].join(''));

        // clean out old message
        userInput.html('');
        // focus on input
        userInput.focus();

        messagesContainer.finish().animate({
            scrollTop: messagesContainer.prop("scrollHeight")
        }, 250);
    }

    function onMetaAndEnter(event) {
        if ((event.metaKey || event.ctrlKey) && event.keyCode == 13) {
            sendNewMessage();
        }
    }
</script>


<script>
    // Toggle for country dropdown
    function toggleDropdown() {
        document.getElementById('dropdown').style.display = 'block';
    }

    // Toggle for service dropdown
    function toggleDropdownservice() {
        document.getElementById('dropdownservice').style.display = 'block';
    }

    // Filter for country items
    function filterItems() {
        const searchInput = document.getElementById('search').value.toLowerCase();
        const items = document.querySelectorAll('#dropdown .item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchInput)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Filter for service items
    function filterItemsservice() {
        const searchInput = document.getElementById('searchservice').value.toLowerCase();
        const items = document.querySelectorAll('#dropdownservice .item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchInput)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Select country
    function selectCountry(element) {
        document.getElementById('search').value = element.textContent;
        document.getElementById('dropdown').style.display = 'none';
        document.getElementById('selectedID').value = element.getAttribute('data-id');
    }

    // Select service
    function selectService(element) {
        document.getElementById('searchservice').value = element.textContent;
        document.getElementById('dropdownservice').style.display = 'none';
        document.getElementById('serviceID').value = element.getAttribute('data-id');
    }

    // Close dropdowns if clicked outside
    document.addEventListener('click', function (event) {
        const searchContainerCountry = document.querySelector('#search');
        const searchContainerService = document.querySelector('#searchservice');

        if (!searchContainerCountry.contains(event.target)) {
            document.getElementById('dropdown').style.display = 'none';
        }

        if (!searchContainerService.contains(event.target)) {
            document.getElementById('dropdownservice').style.display = 'none';
        }
    });
</script>




</body>
</html>
