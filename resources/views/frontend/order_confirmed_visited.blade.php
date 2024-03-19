@extends('frontend.layouts.app')

@section('content')
    <section class="py-4">
        <div class="container text-left">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    
                    <div class="text-center py-4 mb-4">
                        <i class="la la-check-circle la-3x text-success mb-3"></i>
                        <h1 class="h3 mb-3 fw-600">{{ translate('Thank You for Your Order!')}}</h1>
                        <p class="opacity-70 font-italic">{{  translate('A copy or your order summary has been sent to') }} {{ $order->email }}</p>
                    </div>
                    <div class="mb-4 bg-white p-4 rounded shadow-sm">
                        <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Summary')}}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Order date')}}:</td>
                                        <td>{{ $order->date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Name')}}:</td>
                                        <td>{{ $order->first_name }} {{ $order->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Email')}}:</td>
                                        <td>{{ $order->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Shipping address')}}:</td>
                                        <td>{{ $order->adresse }}, {{ $order->city }}, Morocco</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Order status')}}:</td>
                                        <td>{{ $order->payment_status }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Total order amount')}}:</td>
                                        <td>{{ single_price($order->price_product * $order->quantite) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Shipping')}}:</td>
                                        <td>{{ $order->shipping_type }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Payment method')}}:</td>
                                        <td>{{ translate('Paiement à la livraison') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-body">
                                <div class="text-center py-4 mb-4">
                                    <h2 class="h5">{{ translate('Order Code:')}} <span class="fw-700 text-primary"></span></h2>
                                </div>
                                <div>
                                    <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Details')}}</h5>
                                    <div>
                                        <table class="table table-responsive-md">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th width="30%">{{ translate('Product')}}</th>
                                                    <th>{{ translate('Variation')}}</th>
                                                    <th>{{ translate('Quantity')}}</th>
                                                    <th>{{ translate('Delivery Type')}}</th>
                                                    <th class="text-right">{{ translate('Price')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                    <tr>
                                                        <td>{{ $order->id }}</td>
                                                        <td>
                                                            {{ $order->name_product }}
                                                        </td>
                                                        <td>
                                                            -
                                                        </td>
                                                        <td>
                                                            {{ $order->quantite }}
                                                        </td>
                                                        <td>
                                                            {{ $order->delivery_status }}
                                                        </td>
                                                        <td class="text-right">{{ single_price($order->price_product * $order->quantite ) }}</td>
                                                    </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-5 col-md-6 ml-auto mr-0">
                                            <table class="table ">
                                                <tbody>
                                                    <tr>
                                                        <th>{{ translate('Subtotal')}}</th>
                                                        <td class="text-right">
                                                            <span class="fw-600">{{ single_price($order->price_product * $order->quantite ) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Shipping')}}</th>
                                                        <td class="text-right">
                                                            <span class="font-italic">{{ single_price(0) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Tax')}}</th>
                                                        <td class="text-right">
                                                            <span class="font-italic">{{ single_price(0) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Coupon Discount')}}</th>
                                                        <td class="text-right">
                                                            <span class="font-italic">{{ single_price(0) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th><span class="fw-600">{{ translate('Total')}}</span></th>
                                                        <td class="text-right">
                                                            <strong><span>{{ single_price($order->price_product * $order->quantite ) }}</span></strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </section>
@endsection
