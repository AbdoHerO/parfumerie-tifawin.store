<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{  translate('INVOICE') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
	<style media="all">
        @page {
			margin: 0;
			padding:0;
		}
		body{
			font-size: 0.875rem;
            font-family: '<?php echo  $font_family ?>';
            font-weight: normal;
            direction: <?php echo  $direction ?>;
            text-align: <?php echo  $text_align ?>;
			padding:0;
			margin:0; 
		}
		.gry-color *,
		.gry-color{
			color:#000;
		}
		table{
			width: 100%;
		}
		table th{
			font-weight: normal;
		}
		table.padding th{
			padding: .25rem .7rem;
		}
		table.padding td{
			padding: .25rem .7rem;
		}
		table.sm-padding td{
			padding: .1rem .7rem;
		}
		.border-bottom td,
		.border-bottom th{
			border-bottom:1px solid #eceff4;
		}
		.text-left{
			text-align:<?php echo  $text_align ?>;
		}
		.text-right{
			text-align:<?php echo  $not_text_align ?>;
		}
	</style>
</head>
<body>
	<div>

		@php
			$logo = get_setting('header_logo');
		@endphp

		<div style="background: #eceff4;padding: 1rem;">
			<table>
				<tr>
					<td>
						@if($logo != null)
							<img src="{{ uploaded_asset($logo) }}" height="30" style="display:inline-block;">
						@else
							<img src="{{ static_asset('assets/img/logo.png') }}" height="30" style="display:inline-block;">
						@endif
					</td>
					<td style="font-size: 1.5rem;" class="text-right strong">{{  translate('INVOICE') }}</td>
				</tr>
			</table>
			<table>
				<tr>
					<td style="font-size: 1rem;" class="strong">{{ get_setting('site_name') }}</td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small">{{ get_setting('contact_address') }}</td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small">{{  translate('Email') }}: {{ get_setting('contact_email') }}</td>
					<td class="text-right small"><span class="gry-color small">{{  translate('Order ID') }}:</span> <span class="strong">{{ $order->id }}</span></td>
				</tr>
				<tr>
					<td class="gry-color small">{{  translate('Phone') }}: {{ get_setting('contact_phone') }}</td>
					<td class="text-right small"><span class="gry-color small">{{  translate('Order Date') }}:</span> <span class=" strong">{{$order->date}}</span></td>
				</tr>
			</table>

		</div>

		<div style="padding: 1rem;padding-bottom: 0">
            <table>
				@php
					$shipping_address = json_decode($order->shipping_address);
				@endphp
				<tr><td class="strong small gry-color">{{ translate('Bill to') }}:</td></tr>
				<tr><td class="strong">{{$order->last_name}} {{$order->first_name}}</td></tr>
				<tr><td class="gry-color small">{{ $order->adresse }}, {{ $order->city }}</td></tr>
				{{-- <tr><td class="gry-color small">{{ translate('Email') }}: {{ $shipping_address->email }}</td></tr> --}}
				<tr><td class="gry-color small">{{ translate('Phone') }}: {{ $order->phone }}</td></tr>
			</table>
		</div>

	    <div style="padding: 1rem;">
			<table class="padding text-left small border-bottom">
				<thead>
					<tr class="bg-trans-dark">
						<th data-breakpoints="lg" class="min-col">#</th>
						<th class="text-uppercase">{{translate('Description')}}</th>
						<th data-breakpoints="lg" class="text-uppercase">{{translate('Delivery Type')}}</th>
						<th data-breakpoints="lg" class="min-col text-center text-uppercase">{{translate('Qty')}}</th>
						<th data-breakpoints="lg" class="min-col text-center text-uppercase">{{translate('Price')}}</th>
						<th data-breakpoints="lg" class="min-col text-right text-uppercase">{{translate('Total')}}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>{{ $order->id }}</td>
						<td>
							<strong class="text-muted">{{$order->name_product}}</strong>
						</td>
						<td>
							@if ($order->shipping_type != null && $order->shipping_type == 'home_delivery')
							{{ translate('Home Delivery') }}
							@endif

							
						</td>
						<td class="text-center">{{ $order->quantite }}</td>
						<td class="text-center">{{ single_price($order->price_product) }}</td>
						<td class="text-center">{{ single_price($order->price_product * $order->quantite) }}</td>
					</tr>
				</tbody>
			</table>
		</div>

	    <div style="padding:0 1.5rem;">
	        <table class="text-right sm-padding small strong">
	        	<thead>
	        		<tr>
	        			<th width="60%"></th>
	        			<th width="40%"></th>
	        		</tr>
	        	</thead>
		        <tbody>
			        <tr>
			            <td>
					        <table class="text-right sm-padding small strong">
						        <tbody>
									<tr>
										<td>
											<strong class="text-muted">{{translate('Sub Total')}} :</strong>
										</td>
										<td>
											{{ single_price($order->price_product * $order->quantite) }}
										</td>
									</tr>
									<tr>
										<td>
											<strong class="text-muted">{{translate('Tax')}} :</strong>
										</td>
										<td>
											{{ single_price(0) }}
										</td>
									</tr>
									<tr>
										<td>
											<strong class="text-muted">{{translate('Shipping')}} :</strong>
										</td>
										<td>
											{{ single_price(0) }}
										</td>
									</tr>
									<tr>
										<td>
											<strong class="text-muted">{{translate('Coupon')}} :</strong>
										</td>
										<td>
											{{ single_price(0) }}
										</td>
									</tr>
									<tr>
										<td>
											<strong class="text-muted">{{translate('TOTAL')}} :</strong>
										</td>
										<td class="text-muted h5">
											{{ single_price($order->price_product * $order->quantite) }}
										</td>
									</tr>
								</tbody>
						    </table>
			            </td>
			        </tr>
		        </tbody>
		    </table>
	    </div>

	</div>
</body>
</html>
