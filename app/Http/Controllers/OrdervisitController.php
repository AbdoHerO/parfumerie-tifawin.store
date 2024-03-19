<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Ordervisit;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Auth;
use CoreComponentRepository;
use Illuminate\Support\Facades\Http;


class OrdervisitController extends Controller
{

    public function __construct()
    {
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
    public function index(Request $request)
    {
        ////CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $sort_search = null;
        $delivery_status = null;

        $orders = Ordervisit::orderBy('id', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($date != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }
        $orders = $orders->paginate(15);
        return view('backend.sales.all_orders_visitor.index', compact('orders', 'sort_search', 'delivery_status', 'date'));
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
        $global_total = 0;
        $variant = "";
        $quantite = intval($request->quantite);
        $unit_price = intval($request->unit_price);

        if (isset($request->variant)) {
            $variant = $request->variant;
        }

        $global_total += $quantite * $unit_price;

        // -------------------- Add order simple in table orders START---------------------

        $shippingAddress = [];

        $shippingAddress['name']        = $request->last_name . " " . $request->first_name;
        $shippingAddress['email']       = $request->email;
        $shippingAddress['address']     = $request->adresse;
        $shippingAddress['country']     = "Morocco";
        $shippingAddress['state']       = "";
        $shippingAddress['city']        = $request->city;
        $shippingAddress['postal_code'] = "";
        $shippingAddress['phone']       = $request->phone;

        $order = new Order();
        $order->user_id = "0"; // user visitor : user_type = guest
        $order->shipping_address = json_encode($shippingAddress);
        $order->payment_type = "cash_on_delivery"; // cash on delivery
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';
        $order->code = date('Ymd-His') . rand(10, 99);
        $order->date = strtotime('now');
        $order->shipping_type = "home_delivery"; // home delivery
        $order->save();

        // -------------------- lessen Quantity from product and increase number of sale for this product START---------------------

        $product = Product::find($request->id_product);



        //$product_variation = $variant;
        //$product_stock = $product->stocks->where('variant', $product_variation)->first();

        if ($product) {
            $product_variation = $variant;
            $product_stock = $product->stocks->where('variant', $product_variation)->first();

            if ($product->digital != 1 && $product_stock && $quantite > $product_stock->qty) {
                flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                $order->delete();
                return back();
            } elseif ($product->digital != 1 && $product_stock) {
                $product_stock->qty -= $quantite;
                $product_stock->save();
            }

            $product->num_of_sale += $quantite;

            $product->save();
            // return response()->json([
            //         "result" => "no",
            //         "data" => $product_stock,
            //         "product_variation" => $product_variation
            //     ]);

            // -------------------- lessen Quantity from product and increase number of sale for this product END---------------------

            $order_detail = new OrderDetail();
            $order_detail->order_id = $order->id;
            $order_detail->seller_id = $product->user_id;
            $order_detail->product_id = $request->id_product;
            $order_detail->variation = $product_variation;
            $order_detail->price = $unit_price * $quantite;
            $order_detail->tax = 0;
            $order_detail->shipping_type = "home_delivery"; // home delivery
            // $order_detail->product_referral_code = "";
            // $order_detail->shipping_cost = 0.00;

            // $shipping += $order_detail->shipping_cost;
            //End of storing shipping cost

            $order_detail->quantity = $quantite;
            $order_detail->save();



            $order->seller_id = $product->user_id;
            $order->grand_total = $global_total; // without : $subtotal + $tax + $shipping;
            $order->save();



            // -------------------- Add order simple in table orders END---------------------




            // -------------------- Add order to Sheet file (Excel) START---------------------

            // $url = "https://sheetdb.io/api/v1/" . get_setting('sheet_db');
            // #product	#date	#nom_complet	#telephone	#ville	#adresse	#qte	#prix	#status	#livraison	#frais_livraison
            // $params =
            //     [
            //         '#produit' => $request->name_product,
            //         '#date' => Carbon::now() . '',
            //         '#nom_complet' => $request->first_name . ' ' . $request->last_name,
            //         '#telephone' => $request->phone,
            //         '#ville' => $request->city,
            //         '#adresse' => $request->adresse,
            //         '#qte' => $quantite,
            //         '#prix' => $unit_price,
            //         '#status' => 'en attente',
            //         '#livraison' => '',
            //         '#frais_livraison' => '',
            //     ];

            // $response_post_store = Http::asForm()->post($url, $params);
            // $response_get = Http::get($url);
            // dd($response_post_store);

            // -------------------- Add order to Sheet file (Excel) END---------------------


            // -------------------- Add order in table ordervisitor START---------------------

            // $order = new Ordervisit();
            // $order->first_name = $request->first_name;
            // $order->last_name = $request->last_name;
            // $order->email = $request->email ? $request->email : "";
            // $order->phone = $request->phone;
            // $order->city = $request->city;
            // $order->adresse = $request->adresse;
            // $order->id_product = $request->id_product;
            // $order->quantite = $request->quantite;
            // $order->date = Carbon::now();
            // $order->name_product = $request->name_product;
            // $order->price_product = $request->price_product;
            // $order->variant = $request->variant;

            // $order->save();

            $order = DB::table('ordervisits')->insertGetId([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email ? $request->email : "",
                'phone' => $request->phone,
                'city' => $request->city,
                'adresse' => $request->adresse,
                'id_product' => $request->id_product,
                'quantite' => $quantite,
                'date' => Carbon::now(),
                'name_product' => $request->name_product,
                'price_product' => $unit_price,
                'variant' => $variant
            ]);

            $order = Ordervisit::find($order);

            // dd($order);

            // $order = Ordervisit::where('id', $order)->get();
            // $order = DB::table('ordervisits')->where('id','=',$order)->get();

            // -------------------- Add order in table ordervisits END---------------------



            // request()->session()->flash('success', "Your order has been sent");
            // return view('frontend.order_confirmed_visited', compact('order'));
            if ($request->from_landing_page == true) {

                return response()->json([
                    "result" => "ok",
                    "message" => "The product has been ordered succefully"
                ]);
            } else {
                return redirect()->back()->with('success', 'Commande enregistrée avec succès!');
            }
        } else {
            // Handle the case where the product is not found.
            // For instance, you can return an error response or redirect.
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }

        // return back();
        // return back();


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ordervisit  $ordervisit
     * @return \Illuminate\Http\Response
     */
    public function show(Ordervisit $ordervisit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ordervisit  $ordervisit
     * @return \Illuminate\Http\Response
     */
    public function edit(Ordervisit $ordervisit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ordervisit  $ordervisit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ordervisit $ordervisit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ordervisit  $ordervisit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Ordervisit::findOrFail($id);
        if ($order != null) {
            $order->delete();
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function bulk_order_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $order_id) {
                $this->destroy($order_id);
            }
        }

        return 1;
    }

    public function all_orders_show($id)
    {
        $order = Ordervisit::findOrFail(decrypt($id));

        return view('backend.sales.all_orders_visitor.show', compact('order'));
    }

    public function update_delivery_status(Request $request)
    {
        DB::table('ordervisits')->where('id', $request->order_id)->update([
            'delivery_status' => $request->status
        ]);
        return 1;
    }

    public function update_tracking_code(Request $request)
    {
        // $order = Ordervisit::findOrFail($request->order_id);
        // $order->tracking_code = $request->tracking_code;
        // $order->save();

        return 1;
    }

    public function update_payment_status(Request $request)
    {
        DB::table('ordervisits')->where('id', $request->order_id)->update([
            'payment_status' => $request->status
        ]);
        return 1;
    }
}
