@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form id="loginForm">
                        @csrf

                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button onclick="smsLogin();" type="button" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                
                console.log("login form submitting");
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method: 'post',
                    url: '{{ route('login') }}',
                    data: {
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
@endsection
