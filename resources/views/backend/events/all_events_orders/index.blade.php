@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">All Event of Orders</h5>
            </div>
            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                    <option value="">{{translate('Filter by Delivery Status')}}</option>
                    <option value="pending" @if ($delivery_status=='pending' ) selected @endif>{{translate('Pending')}}</option>
                    <option value="confirmed" @if ($delivery_status=='confirmed' ) selected @endif>{{translate('Confirmed')}}</option>
                    <!-- <option value="picked_up" @if ($delivery_status=='picked_up' ) selected @endif>{{translate('Picked Up')}}</option> -->
                    <!-- <option value="on_the_way" @if ($delivery_status=='on_the_way' ) selected @endif>{{translate('On The Way')}}</option> -->
                    <option value="delivered" @if ($delivery_status=='delivered' ) selected @endif>{{translate('Delivered')}}</option>
                    <option value="cancelled" @if ($delivery_status=='cancelled' ) selected @endif>{{translate('Cancel')}}</option>
                </select>
            </div>
            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="payment_status" id="payment_status">
                    <option value="">{{translate('Filter by Payment Status')}}</option>
                    <option value="paid" @isset($payment_status) @if($payment_status=='paid' ) selected @endif @endisset>{{translate('Paid')}}</option>
                    <option value="unpaid" @isset($payment_status) @if($payment_status=='unpaid' ) selected @endif @endisset>{{translate('Un-Paid')}}</option>
                </select>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="staff_search" name="staff_search"@isset($staff_search) value="{{ $staff_search }}" @endisset placeholder="Search by Staff">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="client_search" name="client_search"@isset($client_search) value="{{ $client_search }}" @endisset placeholder="Search by Client name">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>User Code</th>
                        <th data-breakpoints="md">Full name</th>
                        <th data-breakpoints="md">Type user</th>
                        <th data-breakpoints="md">Email</th>
                        <th data-breakpoints="md">Client name</th>
                        <th data-breakpoints="md">Total Order</th>
                        <th data-breakpoints="md">Event Action</th>
                        <th data-breakpoints="md">Status Operation</th>
                        <th data-breakpoints="md">Date Event</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $key => $event)
                    <tr>
                        <td>
                            #{{ $event->user_id }}
                        </td>
                        <td>
                            {{ $event->user_name }}
                        </td>
                        <td>
                            {{ $event->user_type }}
                        </td>
                        <td>
                            {{ $event->user_email }}
                        </td>
                        <td>
                            #ID{{ $event->order_ide }} <br>{{ $event->order_user_name }}
                        </td>
                        <td>
                            {{ single_price($event->order_price_total) }}
                        </td>
                        <td>
                            {{ $event->type_order_event }}
                        </td>
                        <td>
                            @if ($event->status_order_event == 'pending' )
                            <span class="badge badge-inline badge-warning">{{translate('Pending')}}</span>
                            @elseif ($event->status_order_event == 'confirmed')
                            <span class="badge badge-inline badge-success">{{translate('Confirmed')}}</span>
                            @elseif ($event->status_order_event == 'delivered')
                            <span class="badge badge-inline badge-success">{{translate('Delivered')}}</span>
                            @elseif ($event->status_order_event == 'cancelled')
                            <span class="badge badge-inline badge-danger">{{translate('Cancel')}}</span>
                            @elseif ($event->status_order_event == 'picked_up')
                            <span class="badge badge-inline badge-info">{{translate('Picked Up')}}</span>
                            @endif

                            @if ($event->status_order_event == 'paid' )
                            <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                            @elseif ($event->status_order_event == 'unpaid')
                            <span class="badge badge-inline badge-danger">{{translate('Un-Paid')}}</span>
                            @endif
                        </td>
                        <td>
                            {{ $event->date_event }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $events->appends(request()->input())->links() }}
            </div>

        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
    </script>
@endsection
