@extends('layouts.app')

@section('content')
<div class="product-list">
	<div class="row">
		@if(count($products) > 0)
			<table class="table table-hover table-condensed">
				<thead>
					<th>Product Image</th>
					<th>Product Name</th>
					<th>Product Qty</th>
					<th>Product Price</th>
					<th></th>
				</thead>
				@foreach($products as $product)
					@if(isset($product->composite_product_details))
						<tr>
							<td><img src="{{ $product->images[0]->src }}" class="img-responsive" width="50"></td>
							<td>{{ $product->name }}</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						@foreach($product->composite_product_details->components as $component)
							@if(isset($component->products))
								@foreach($component->products as $value)
									<tr>
										<td><img src="{{ $value->images[0]->src }}" class="img-responsive" width="50"></td>
										<td>{{ $value->name }}</td>
										<td><input type="number" value="1" min="1" class="form-control qty-{{ $value->id }}" style="width:100px;"></td>
										<td>
											${{ $value->variations[0]->regular_price }}
											<input type="hidden" value="{{ $value->variations[0]->regular_price }}" class="price-{{ $value->id }}" style="width:100px;">
										</td>
										<td><a href="{{ url('add-to-cart?id='.$value->id.'&image='.$value->images[0]->src.'&name='.$value->name) }}" class="blue-btn add-to-cart" data-id="{{ $value->id }}">Add to cart</a></td>
									</tr>
								@endforeach
							@endif
						@endforeach
					@endif
				@endforeach
			</table>
		@endif
	</div>
</div>
<script type="text/javascript">
$(function(){
	$('.add-to-cart').on('click', function(){
		var id = $(this).attr('data-id');
		var url = $(this).attr('href');
		var qty = $('.qty-'+id).val();
		var price = $('.price-'+id).val();

		var new_url = url+'&qty='+qty+'&price='+price;
		
		window.location.replace(new_url);
		return false;
	})
})
</script>
@endsection