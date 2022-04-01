<?php
use App\Http\Controllers\ProductController;
$notifications = ProductController::notification();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'Laravel') }}</title>

	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <link href="{{ asset('custom.css') }}" rel="stylesheet">
</head>
<body>
	@if(Request::path() != 'order')
		<div class="">
		    <div class="row">
		        <div class="col-lg-12 col-sm-12 col-12 main-section">
		            <div class="dropdown">
		                <button type="button" class="btn btn-info" data-toggle="dropdown">
		                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Cart <span class="badge badge-pill badge-danger">{{ count((array) session('cart')) }}</span>
		                </button>
		                <div class="dropdown-menu">
		                    <div class="row total-header-section">
		                        <div class="col-lg-6 col-sm-6 col-6">
		                            <i class="fa fa-shopping-cart" aria-hidden="true"></i> <span class="badge badge-pill badge-danger">{{ count((array) session('cart')) }}</span>
		                        </div>
		                        @php $total = 0 @endphp
		                        @foreach((array) session('cart') as $id => $details)
		                            @php $total += $details['price'] * $details['quantity'] @endphp
		                        @endforeach
		                        <div class="col-lg-6 col-sm-6 col-6 total-section text-right">
		                            <p>Total: <span class="text-info">$ {{ $total }}</span></p>
		                        </div>
		                    </div>
		                    @if(session('cart'))
		                        @foreach(session('cart') as $id => $details)
		                            <div class="row cart-detail">
		                                <div class="col-lg-4 col-sm-4 col-4 cart-detail-img">
		                                    <img src="{{ $details['image'] }}" />
		                                </div>
		                                <div class="col-lg-8 col-sm-8 col-8 cart-detail-product">
		                                    <p>{{ $details['name'] }}</p>
		                                    <span class="price text-info"> ${{ $details['price'] }}</span> <span class="count"> Quantity:{{ $details['quantity'] }}</span>
		                                </div>
		                            </div>
		                        @endforeach
		                    @endif
		                    <div class="row">
		                        <div class="col-lg-12 col-sm-12 col-12 text-center checkout">
		                            <a href="{{ route('cart') }}" class="btn btn-primary btn-block">View all</a>
		                        </div>
		                    </div>
		                </div>
		            </div>

		            <div class="dropdown">
		            	<button type="button" class="btn btn-info" data-toggle="dropdown">
		                    <i class="fa fa-solid fa-bell"></i> <span class="badge badge-pill badge-danger" id="notification_count">{{ count($notifications) }}</span>
		                </button>
		                <div class="dropdown-menu">
		                	<div class="row total-header-section" id="notification-section">
		                		
	                			@if(count($notifications) > 0)
	                				@foreach($notifications as $notification)
	                					<a href="{{ url('order-detail/'.$notification->order_id.'?is_read='.$notification->id) }}" class="notify-link">
		                					<div class="col-lg-12 border-top">
		                						<p>{{ $notification->name }}</p>
		                					</div>
		                				</a>
	                				@endforeach
	                			@else
	                				<div class="col-lg-12">
	                					<p>No new notification found!</p>
	                				</div>
	                			@endif
		                	</div>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
	@endif
	<div class="container-fluid">
		@if(session('success'))
			<div class="alert alert-success">
				{{ session('success') }}
			</div> 
		@endif
		@if(session('danger'))
			<div class="alert alert-danger">
				{{ session('danger') }}
			</div> 
		@endif
		@yield('content')
	</div>
</body>
<script type="text/javascript">
function latest_notification(){
	$.ajax({
		url: "{{ route('latest-notification') }}",
		method: 'get',
		data: {},
		dataType: 'json',
		success: function (response) {
			$('#notification_count').html(response.count);
			$('#notification-section').html(response.html);
		}
	});
}
$(function(){
	setInterval(function () {
		latest_notification();
	}, 10000);
})
</script>
</html>