<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;

class ProductController extends Controller
{
    public function index(Request $request){
        $username = env('API_USERNAME');
        $password = env('API_PASSWORD');
        $token = base64_encode($username.':'.$password);
        
        $url = "https://mangomart-autocount.myboostorder.com/wp-json/wc/v1/products";

        $results = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.$token
        ])->get($url);
        $total_page = $results->headers()['X-WP-TotalPages'][0];
        $products = json_decode($results);
        return view('products.index', compact('products', 'total_page'));
    }

    public static function product_detail($product_id){
        $username = env('API_USERNAME');
        $password = env('API_PASSWORD');
        $token = base64_encode($username.':'.$password);
        
        $url = "https://mangomart-autocount.myboostorder.com/wp-json/wc/v1/products/".$product_id;

        $results = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.$token
        ])->get($url);
        
        $product = json_decode($results);
        return $product;
    }

    public function cart()
    {
        return view('products.cart');
    }

    public function addToCart(Request $request){
        $id = $request->id;
        $cart = session()->get('cart', []);

        if(isset($cart[$id])){
            $cart[$id]['quantity']++;
        }else{
            $cart[$id] = [
                "name" => $request->name,
                "id" => $request->id,
                "quantity" => $request->qty,
                "price" => $request->price,
                "image" => $request->image
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }
    }

    public function checkout()
    {
        $total = 0;
        $quantity = 0;
        foreach((array) session('cart') as $id => $details){
            $total += $details['price'] * $details['quantity'];
            $quantity += $details['quantity'];
        }
        return view('products.checkout', compact('total'));
    }

    public function placeOrder(Request $request)
    {
        if(count(session('cart')) > 0){
            $total = 0;
            $quantity = 0;
            foreach((array) session('cart') as $id => $details){
                $total += $details['price'] * $details['quantity'];
                $quantity += $details['quantity'];
            }
            
            $order = Order::create([
                'order_number'      =>  'ORD-'.strtoupper(uniqid()),
                'status'            =>  'pending',
                'grand_total'       =>  $total,
                'item_count'        =>  $quantity
            ]);

            if($order){
                foreach(session('cart') as $id => $details){
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $details['id'],
                        'quantity' => $details['quantity'],
                        'price' => $details['price'] * $details['quantity']
                    ]);
                }
            }
            if(session()->has('cart')){
                session()->forget('cart');
            }

            return redirect('/')->with('success', 'Order placed successfully!');
        }else{
            return redirect('/')->with('danger', 'Please add product in your cart!');
        }
    }

    public function orderlist(){
        $orders = Order::with('items')->get();
        return view('orders.index', compact('orders'));
    }

    public function orderdetail(Request $request, $id){
        $order = Order::with('items')->where('id', $id)->first();
        if(!empty($request->is_read)){
            Notification::where('id', $request->is_read)->update([
                'read' => 1
            ]);
            return redirect()->route('order.detail', [$id]);
        }
        return view('orders.detail', compact('order'));
    }

    public static function notification(){
        $data = Notification::where('read', 0)->orderby('created_at', 'desc')->get();
        return $data;
    }

    public function saveNotification(Request $request){
        $order = Order::where('id', $request->order_id)->first();
        if($order){
            Order::where('id', $request->order_id)->update([
                'status' => $request->status
            ]);

            $msg = $order->order_number.' status updated to '.$request->status.' from '.$order->status;
            Notification::create([
                'order_id' => $request->order_id,
                'name' => $msg
            ]);

            echo json_encode(['update_status' => 1]);exit;
        }
        echo json_encode(['update_status' => 0]);exit;
    }

    public function latestNotification(){
        $data = Notification::where('read', 0)->orderby('created_at', 'desc')->get();
        $html = "";
        $count = count($data);
        if(count($data) > 0){
            foreach($data as $value){
                $html .= '<a href="'.url('order-detail/'.$value->order_id.'?is_read='.$value->id).'" class="notify-link">
                    <div class="col-lg-12 border-top">
                        <p>'.$value->name.'</p>
                    </div>
                </a>';
            }
        }else{
            $html .= '<div class="col-lg-12">
                <p>No new notification found!</p>
            </div>';
        }
        $data['count'] = $count;
        $data['html'] = $html;
        echo json_encode($data);exit;
    }
}
