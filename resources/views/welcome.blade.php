<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    
        <!-- Custom Css -->
        <link rel="stylesheet" href="{{ asset('/css/style.css') }}">

    <style>
        .main-content{
            margin-top: 50px
        }
    </style>
    
    </head>
    <body>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
          <div class="container">
            <a class="navbar-brand" href="#">LaraEc</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
              <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                  <a class="nav-link" href="#">Home
                    <span class="sr-only">(current)</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Services</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Contact</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      
        <!-- Page Content -->
        <div class="container">
      
          <div class="row main-content">
      
            <div class="col-lg-3">
      
              <h1 class="my-4">LaraEc</h1>
              <div class="list-group">
                <a href="#" class="list-group-item">Category 1</a>
                <a href="#" class="list-group-item">Category 2</a>
                <a href="#" class="list-group-item">Category 3</a>
              </div>
      
            </div>
            <!-- /.col-lg-3 -->
      
            <div class="col-lg-9">
      
              <div id="carouselExampleIndicators" class="carousel slide my-4" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                  <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                  <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="carousel-item active">
                    <img class="d-block img-fluid" src="http://placehold.it/900x350" alt="First slide">
                  </div>
                  <div class="carousel-item">
                    <img class="d-block img-fluid" src="http://placehold.it/900x350" alt="Second slide">
                  </div>
                  <div class="carousel-item">
                    <img class="d-block img-fluid" src="http://placehold.it/900x350" alt="Third slide">
                  </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
      
              <div class="row">
      
                @foreach($products as $product)
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card h-100">
                    <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>
                    <div class="card-body">
                      <h4 class="card-title">
                        <a href="#">{{ $product->name }}</a>
                      </h4>
                      <h5>{{ $product->price }}</h5>
                      <p class="card-text">{{ $product->description }}</p>
                    </div>
                    <div class="card-footer">
                      <small class="text-muted">&#9733; &#9733; &#9733; &#9733; &#9734;</small>
                      <p class="btn-holder"><a href="{{ url('add-to-cart/'.$product->id) }}" class="btn btn-warning btn-block text-center" role="button">Add to cart</a> </p>
                    </div>
                  </div>
                </div>
                @endforeach
      
              </div>
              <!-- /.row -->

              <table id="cart" class="table table-hover table-condensed">
                <thead>
                  <tr>
                      <th style="width:50%">Product</th>
                      <th style="width:10%">Price</th>
                      <th style="width:8%">Quantity</th>
                      <th style="width:22%" class="text-center">Subtotal</th>
                      <th style="width:10%"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php $total = 0 ?>
 
                  @if(session('cart'))
                      @foreach(session('cart') as $id => $details)
           
                          <?php $total += $details['price'] * $details['quantity'] ?>
                          <tr>
                              <td data-th="Product">
                                  <div class="row">
                                      <div class="col-sm-3 hidden-xs"><img src="data:image/png;base64,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" width="100" height="100" class="img-responsive"/></div>
                                      <div class="col-sm-9">
                                          <h4 style="margin-left: 12px;" class="nomargin">{{ $details['name'] }}</h4>
                                      </div>
                                  </div>
                              </td>
                              <td data-th="Price">${{ $details['price'] }}</td>
                              <td data-th="Quantity">
                                  <input type="number" value="{{ $details['quantity'] }}" class="form-control quantity" />
                              </td>
                              <td data-th="Subtotal" class="text-center">${{ $details['price'] * $details['quantity'] }}</td>
                              <td class="actions" data-th="">
                                  <button class="btn btn-info btn-sm update-cart" data-id="{{ $id }}"><i class="fa fa-refresh"></i></button>
                                  <button class="btn btn-danger btn-sm remove-from-cart" data-id="{{ $id }}"><i class="fa fa-trash-o"></i></button>
                              </td>
                          </tr>

                      @endforeach
                    @endif

                </tbody>
                <tfoot>
                  <tr class="visible-xs">
                      <td class="text-center"><strong>Total {{ $total }}</strong></td>
                  </tr>
                  <tr>
                      <td><a href="{{ url('/') }}" class="btn btn-warning"><i class="fa fa-angle-left"></i> Continue Shopping</a></td>
                      <td colspan="2" class="hidden-xs"></td>
                      <td class="hidden-xs text-center"><strong>Total ${{ $total }}</strong></td>
                  </tr>
                  </tfoot>
                  <tr>
                    <a href="/payment-initialize">Checkout</a>
                  </tr>
              </table>
                  
            </div>
            <!-- /.col-lg-9 -->
      
          </div>
          <!-- /.row -->
      
        </div>
        <!-- /.container -->
      
        <!-- Footer -->
        <footer class="py-5 bg-dark">
          <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Your Website 2019</p>
          </div>
          <!-- /.container -->
        </footer>
      
        <!-- Bootstrap core JavaScript -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script>
          (function (window, document) {
              var loader = function () {
                  var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
                  script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
                  tag.parentNode.insertBefore(script, tag);
              };
      
              window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
          })(window, document);
      </script>
    </body>
</html>
