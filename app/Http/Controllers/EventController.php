<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Auth;
use CoreComponentRepository;
use Illuminate\Support\Facades\Http;


class EventController extends Controller
{
    
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_orders'])->only('all_orders');
        $this->middleware(['permission:view_inhouse_orders'])->only('all_orders');
        $this->middleware(['permission:view_seller_orders'])->only('all_orders');
        $this->middleware(['permission:view_pickup_point_orders'])->only('all_orders');
        $this->middleware(['permission:view_order_details'])->only('show');
        $this->middleware(['permission:delete_order'])->only('destroy');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_events_products(Request $request)
    {
        ////CoreComponentRepository::instantiateShopRepository();
        
        $date = $request->date;
        $staff_search = null;
        $product_search = null;
        $event_type = null;

        $events = Event::orderBy('id', 'desc')->whereNotNull('product_id');

        if ($request->has('staff_search')) {
            $staff_search = $request->staff_search;
            $events = $events->where('user_name', 'like', '%' . $staff_search . '%');
        }
        if ($request->has('product_search')) {
            $product_search = $request->product_search;
            $events = $events->where('product_name', 'like', '%' . $product_search . '%');
        }
        if ($request->has('event_type')) {
            $event_type = $request->event_type;
            $events = $events->where('type_event', 'like', '%' . $event_type . '%');
        }
        if ($date != null) {
            $events = $events->where('date_event', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])).'  00:00:00')
            ->where('date_event', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])).'  23:59:59');
        }

        $events = $events->paginate(15);
        return view('backend.events.all_events_products.index', compact('events', 'event_type', 'product_search', 'staff_search', 'date'));
    }

    public function get_events_orders(Request $request)
    {
        ////CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $client_search = null;
        $staff_search = null;
        $delivery_status = null;
        $payment_status = null;
        
        $events = Event::orderBy('id', 'desc')->whereNotNull('order_id');
        
        if ($request->has('staff_search')) {
            $staff_search = $request->staff_search;
            $events = $events->where('user_name', 'like', '%' . $staff_search . '%');
        }
        if ($request->has('client_search')) {
            $client_search = $request->client_search;
            $events = $events->where('order_user_name', 'like', '%' . $client_search . '%');
        }
        if ($date != null) {
            $events = $events->where('date_event', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])).'  00:00:00')
            ->where('date_event', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])).'  23:59:59');
        }

        if ($request->delivery_status != null) {
            $events = $events->where('status_order_event', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->payment_status != null) {
            $events = $events->where('status_order_event', $request->payment_status);
            $payment_status = $request->payment_status;
        }

        $events = $events->paginate(15);
        return view('backend.events.all_events_orders.index', compact('events', 'client_search', 'staff_search', 'delivery_status','payment_status', 'date'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    
}
