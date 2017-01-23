<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Gas Stations App</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
       	<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/style.css') }}">
       	<script type="text/javascript"
      		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDu2egAzwpHKGraG-z3QOwDyLbjJ1-jgEg">
    	</script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="{{ URL::asset('js/script.js') }}"></script>

	</head>
    <body>
      @if((Auth::user()) !=null && (Auth::user()->is_owner==0))
        <ul class="navigation">
          <li class="nav-item">
            <a href="#" data-toggle="modal" data-target="#NumberModal" onclick="getNumberOfGasStations()">  Show No of GasStations
            </a>
          </li>
          <li class="nav-item">
            <a href="#" data-toggle="modal" data-target="#StatsModal">Show Stats</a>
          </li>
          <li class="nav-item">
            <a href="#" data-toggle="modal" data-target="#StationModal">Show Gas Station Prices</a>
            </li>
          <li class="nav-item"><a href="#" onclick="getData()">Show all Data</a></li>
          <li class="nav-item">
            <a href="#" data-toggle="modal" data-target="#OrderModal">Order</a>
          </li>
          <li class="nav-item">
            <a href="#" data-toggle="modal" data-target="#ChartModal">Graphs</a>
          </li>
          <li class="nav-item"><a href="/logout">Log out</a></li>
        </ul>

        <!-- Modal -->
        <div id="OrderModal" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Make an Order</h4>
              </div>
              <div class="modal-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/order') }} ">
                      {{ csrf_field() }}
                      <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                      <div class="row">
                        <div class="col-md-6">
                          <label>Enter price data Id</label>
                        </div>
                        <div class="col-md-6">
                          <input type="hidden" name="priceDataId" id="orderPriceDataId">
                          <select id="orderFuelID" ondblclick="fillFuelIds()" onchange="select()">
                          </select>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <label>Enter desirable quantity</label>
                        </div>
                        <div class="col-md-6">
                          <input id="quantity" type="text" name="quantity" required autofocus>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <div class="row">
                          <div class="col-md-12">
                            <input type="submit" class="btn btn-primary" value="Submit" style="margin: auto; display: block;">
                          </div>
                        </div>
                      </div>
                  </form>
                  
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </div>
        @if (session('status'))
          <script type="text/javascript"> alert("{{ session('status') }}");</script>
        @endif
      @endif
      @if((Auth::user()) !=null && (Auth::user()->is_owner==1))
       <?php
          $gasstation = App\GasStation::where('user_id','=', Auth::user()->id)->first();
      ?>
      <ul class="navigation">
        <li class="nav-item">
          <a href="#" data-toggle="modal" data-target="#NumberModal" onclick="getNumberOfGasStations()">Show No of GasStations</a>
        </li>
        <li class="nav-item">
          <a href="#" data-toggle="modal" data-target="#StatsModal">Show Stats</a>
        </li>
        <li class="nav-item">
          <a href="#" data-toggle="modal" data-target="#StationModal">Show Gas Station Prices</a>
        </li>
        <li class="nav-item"><a href="#" onclick="getData()">Show all Data</a></li>
        <li class="nav-item">
          <a href="#" data-toggle="modal" data-target="#OrdersModal">Order List</a>
        </li>
        <li class="nav-item">
          <a href="#" data-toggle="modal" data-target="#ChangePricesModal">Change Prices</a>
        </li>
        <li class="nav-item">
            <a href="#" data-toggle="modal" data-target="#ChartModal">Graphs</a>
        </li>
        <li class="nav-item"><a href="/logout">Log out</a></li>
      </ul>

     
       <!-- Modal -->
      <div id="OrdersModal" class="modal fade" role="dialog" onshow="getOrders({{ $gasstation->id }})">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Orders</h4>
            </div>
            <div class="modal-body" id="ordersBody" style="text-align: center;">
              
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

       <!-- Modal -->
      <div id="ChangePricesModal" class="modal fade" role="dialog" >
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Orders</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" role="form" method="POST" action="{{ url('/update/'. $gasstation->id) }}" >
                {{ csrf_field() }}
                <input type="hidden" name="priceDataId" id="priceID">
                <div class="row">
                  <div class="col-md-6">
                    <select id="selectPriceId" ondblclick="fillPriceDataIds({{ $gasstation->id }})" onchange="change()">  
                    </select>
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="value" id="value">
                  </div>
                </div>
                <div class="modal-footer">
                  <div class="row">
                    <div class="col-md-12">
                      <input type="submit" class="btn btn-primary" value="Submit" style="margin: auto; display: block;">
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      @if (session('status'))
          <script type="text/javascript"> alert("{{ session('status') }}");</script>
      @endif

      @endif
      @if((Auth::user()) ==null)
        <ul class="navigation">
          <li class="nav-item"><a href="#" data-toggle="modal" data-target="#loginModal">Log In</a></li>
          <li class="nav-item"><a href="#" data-toggle="modal" data-target="#RegisterModal">Register</a></li>
        </ul>

         <!-- Modal -->
        <div id="loginModal" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Log In Page</h4>
              </div>
              <div class="modal-body">
                 <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                          {{ csrf_field() }}

                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                              <div class="col-md-6">
                                  <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                  @if ($errors->has('email'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                          <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                              <label for="password" class="col-md-4 control-label">Password</label>

                              <div class="col-md-6">
                                  <input id="password" type="password" class="form-control" name="password" required>

                                  @if ($errors->has('password'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('password') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                          <div class="form-group">
                              <div class="col-md-6 col-md-offset-4">
                                  <div class="checkbox">
                                      <label>
                                          <input type="checkbox" name="remember"> Remember Me
                                      </label>
                                  </div>
                              </div>
                          </div>

                          <div class="form-group">
                              <div class="col-md-8 col-md-offset-4">
                                  <button type="submit" class="btn btn-primary">
                                      Login
                                  </button>

                                  <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                      Forgot Your Password?
                                  </a>
                              </div>
                          </div>
                      </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </div>

        <!-- Modal -->
        <div id="RegisterModal" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Log In Page</h4>
              </div>
              <div class="modal-body">
                 <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </div>


      @endif


      <input type="checkbox" id="nav-trigger" class="nav-trigger" />
      <label for="nav-trigger"></label>
      <div class='site-wrap' id='site-wrap'>
        
      </div>
     
      <!-- Modal -->
      <div id="NumberModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Number Of Gas Stations</h4>
            </div>
            <div class="modal-body" style="text-align: center;">
              <p>The number of Gas Stations in the Database is :</p>
              <p id="number"></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      <!-- Modal -->
      <div id="StatsModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Show Stats</h4>
            </div>
            <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                  <form id="statForm" class="form-horizontal" role="form" method="GET" action="javascript:showStats()">
                    <div class="row">
                      {{ csrf_field() }}
                      <div class="col-md-6">
                        <label>Give fuel Type</label>
                      </div>
                      <div class="col-md-6">
                        <input type="text" name="fuelTypeID">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label>Give fuel Sub Type</label>
                      </div>
                      <div class="col-md-6">
                        <input type="text" name="fuelSubTypeID">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Search</button>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <p>Min Price = <span id="min"></p>
                      <p>Max Price = <span id="max"></p>
                      <p>Avg Price = <span id="avg"></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      <!-- Modal -->
      <div id="StationModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Show Gas Station Prices</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-4">
                  <label style="margin-top: 5px;">Peek Gas Stastion</label>
                </div>
                <div class="col-md-4">
                  <select id="stationId" ondblclick="fillIds()"" style="margin-top: 5px;">  
                  </select>
                </div>
                <div class="col-md-4">
                  <button type="button" class="btn btn-primary" onclick="getPrices()">Get Price List</button>
                </div>
              </div>
              <div class="row"><br></div>
              <div class="row">
                <div class="col-md-12" id="priceCol">
                  
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      <!-- Modal -->
      <div id="ChartModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"">&times;</button>
              <h4 class="modal-title">Charts</h4>
            </div>
            <div class="modal-body">
                <div class="row" style="align-content: center;">
                  <div class="col-md-12" style="margin: auto; text-align: center;">
                  <form action="javascript:getSelection()">
                    <select id="ChartType">
                      <option value="pie">Pie Chart</option>
                      <option value="column">Column Chart</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Select</button>
                  </form>
                  </div>
                </div>
                <div id="Chart" style="width: match-parent; height: 350px;"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

	</body>
</html>
