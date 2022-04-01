@extends('layouts.app')

@section('content')
<div class="alert alert-success" id="order_status_msg" style="display:none">
	{{ __("Order Status Updated!") }}
</div>
<div class="alert alert-danger" id="order_status_error" style="display:none">
	{{ __("Something went wrong. Please try again!") }}
</div> 
<table id="cart" class="table table-hover table-striped">
	<thead>
		<tr>
			<th class="text-center">Order #</th>
			<th class="text-center">Total Quantity</th>
			<th class="text-center">Total Price</th>
			<th class="text-center">Status</th>
		</tr>
	</thead>
	<tbody>
		@if(count($orders) > 0)
			@foreach($orders as $order)
				<tr>
					<td>{{ $order->order_number }}</td>
					<td class="text-center">{{ $order->item_count }}</td>
					<td class="text-center">${{ number_format($order->grand_total, 2) }}</td>
					<td>
						<select class="form-control payment_status" name="status" data-id="{{ $order->id }}">
							<option value="pending" {{ ($order->status == 'pending') ? 'selected' : '' }}>Pending</option>
							<option value="processing" {{ ($order->status == 'processing') ? 'selected' : '' }}>Processing</option>
							<option value="completed" {{ ($order->status == 'completed') ? 'selected' : '' }}>Completed</option>
							<option value="decline" {{ ($order->status == 'decline') ? 'selected' : '' }}>Decline</option>
						</select>
					</td>
				</tr>
			@endforeach
		@else
			<tr>
				<td colspan="4" class="text-center">No order found!</td>
			</tr>
		@endif
	</tbody>
</table>
<script type="text/javascript">
$(function(){
	$('.payment_status').on('change', function(){
		var status = $(this).val();
		var order_id = $(this).attr('data-id');

		$.ajax({
			url: "{{ route('send-notification') }}",
			method: 'post',
			data: {
				_token: '{{ csrf_token() }}', 
				order_id: order_id,
				status: status
			},
			dataType: 'json',
			success: function (response) {
				if(response.update_status == 1){
					$('#order_status_msg').show();
					$('#order_status_error').hide();
				}else{
					$('#order_status_error').show();
					$('#order_status_msg').hide();
				}
			}
		});
	});
});
</script>
@endsection