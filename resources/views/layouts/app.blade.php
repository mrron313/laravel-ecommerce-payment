<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script src="https://sdk.accountkit.com/en_US/sdk.js"></script>

    <script>
        // initialize Account Kit with CSRF protection
        AccountKit_OnInteractive = function(){
            AccountKit.init(
                {
                appId:"1283114428536031", 
                state:"{{ csrf_token() }}", 
                version:"v1.0",
                fbAppEventsEnabled:true,
                redirect:"http://ssl-laravel-arif.test/"
                }
            );
            };
    </script>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

        <script src="https://code.jquery.com/jquery-3.4.1.slim.js"></script>

        <script>

            // login callback
            function loginCallback(response) {
              if (response.status === "PARTIALLY_AUTHENTICATED") {

                $.ajax({
                    method: 'post',
                    url: '{{ route('api.otp-verify') }}',
                    data: {'code': response.code, 'csrf': response.state}
                }).done(function (response) {

                    console.log(response);

                    if (response['status'] === true) {
                        
                        console.log("reg form submitting");
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            method: 'post',
                            url: '{{ route('register') }}',
                            data: {
                                'name': document.getElementById("name").value, 
                                'email': document.getElementById("email").value,
                                'phone': response['phone']
                            }
                        }).done(function (response) {
                            location.reload()
                        });

                    } else {
                        swal("Error", response['message'], 'error');
                    }
                });

              }
              else if (response.status === "NOT_AUTHENTICATED") {
                // handle authentication failure
              }
              else if (response.status === "BAD_PARAMS") {
                // handle bad parameters
              }
            }
          
            // phone form submission handler
            function smsLogin() {
              var countryCode = '+880';
              var phoneNumber = document.getElementById("phone").value;
              AccountKit.login(
                'PHONE', 
                {countryCode: countryCode, phoneNumber: phoneNumber}, // will use default values if not specified
                loginCallback
              );
            }
          
        </script>
              
</body>
</html>
