<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = new Collection;
        $orders = Order::where('user_id', Auth::user()->id)->get();
        // $orders = Order::where('user_id', 1)->get();
        foreach ($orders as $order) {
            $products = $order->products()->get();
            $results->push(['order' => $order, 'products' => $products]);
        }

        return response()->json(['results' => $results, 200]);
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
        $order = new Order();
        $user = User::find(Auth::user()->id);
        // $user = User::find(1);

        $order->total_cost = $request->totalPrice;
        $order->user_id = Auth::user()->id;
        // $order->user_id = 1;
        
        $order->save();
        foreach ($request->products as $product)
        {
            $order->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }
        
        $order->user()->associate($user);

        return response()->json(['message' => 'Place Order Successfully!'], 200);
    }
}
