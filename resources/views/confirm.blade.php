<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Payment Confirmation</title>

    <link rel="stylesheet" href="{{ asset('css/vendor/bootstrap.min.css') }}">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="{{ asset('js/html5shiv.min.js') }}"></script>
    <script src="{{ asset('js/respond.min.js') }}"></script>
    <![endif]-->

    <style>
            .row {
            display: -ms-flexbox; /* IE10 */
            display: flex;
            -ms-flex-wrap: wrap; /* IE10 */
            flex-wrap: wrap;
            margin: 0 -16px;
            }

            .col-25 {
            -ms-flex: 25%; /* IE10 */
            flex: 25%;
            }

            .col-50 {
            -ms-flex: 50%; /* IE10 */
            flex: 50%;
            }

            .col-75 {
            -ms-flex: 75%; /* IE10 */
            flex: 75%;
            }

            .col-25,
            .col-50,
            .col-75 {
            padding: 0 16px;
            }

            .container {
            background-color: #f2f2f2;
            padding: 5px 20px 15px 20px;
            border: 1px solid lightgrey;
            border-radius: 3px;
            }

            input[type=text] {
            width: 100%;
            margin-bottom: 20px;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 3px;
            }

            label {
            margin-bottom: 10px;
            display: block;
            }

            .icon-container {
            margin-bottom: 20px;
            padding: 7px 0;
            font-size: 24px;
            }

            .btn {
            background-color: #BC9CFF;
            color: white;
            padding: 12px;
            margin: 10px 0;
            border: none;
            width: 100%;
            border-radius: 3px;
            cursor: pointer;
            font-size: 17px;
            }

            /* .btn:hover {
            background-color: #45a049;
            } */

            span.price {
            float: right;
            color: grey;
            }

    </style>
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
        <div class="col-25">
            <div class="container">
                <h3 style="text-align: center">Payment Confirmation
                    <span class="price" style="color:black">
                    <i class="fa fa-shopping-cart"></i>
                    </span>
                </h3>
                <br>
                <p><a href="#">Amount </a> <span class="price">{{ $order->paid_amount }} BDT</span></p>
                <hr>

                <div class="col-sm-6 col-sm-offset-3">
                <form action="{{ route('payment.pay') }}" method="post">
                    @csrf
                    <input type="hidden" name="name" value='{{ $user->name }}'><br>
                    <input type="hidden" name="email" value='{{ $user->email }}'><br>
                    <input type="hidden" name="payable" value="{{ $order->paid_amount }}"><br>
                    <input type="hidden" name="currency" value="{{ $order->currency }}"><br>
                    <input type="hidden" name="transaction_id" value="{{ $order->transaction_id }}"><br>
                    <button class="btn btn-primary" type="submit">Pay Now</button>
                </form>
            </div>
            </div>
        </div>
</body>
</html>
