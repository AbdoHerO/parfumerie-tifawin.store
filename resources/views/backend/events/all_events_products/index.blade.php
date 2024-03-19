@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">All Event of Products</h5>
            </div>

            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="event_type" id="event_type">
                    <option value="">Event Action (Default)</option>
                    <option value="Added" @if ($event_type == 'Added') selected @endif>Added</option>
                    <option value="Update" @if ($event_type == 'Update') selected @endif>Update</option>
                    <option value="Delete" @if ($event_type == 'Delete') selected @endif>Delete</option>
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
                    <input type="text" class="form-control" id="product_search" name="product_search"@isset($product_search) value="{{ $product_search }}" @endisset placeholder="Search by Product name">
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
                        <th data-breakpoints="md">Product name</th>
                        <th data-breakpoints="md">Event Action</th>
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
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($event->product_image)}}" alt="Image" class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2">#ID:{{ $event->product_id }} / {{$event->product_name }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($event->type_event == 'Added')
                            <span class="badge badge-inline badge-success">Added</span>
                            @elseif ($event->type_event == 'Update')
                            <span class="badge badge-inline badge-warning">Updated</span>
                            @elseif ($event->type_event == 'Delete')
                            <span class="badge badge-inline badge-danger">Deleted</span>
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
