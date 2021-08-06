<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Mail\OrderCompleted;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Devinweb\LaravelHyperpay\Facades\LaravelHyperpay;
use App\Billing\HyperPayBilling;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = \Cart::session(auth()->id())->getContent();

        return view('frontend.checkout.index', compact('cartItems'));

    }

    public function store(StoreOrderRequest $request)
    {
        $order = new Order();
        if ($order) {
            $order['order_number'] = uniqid('OrderNumber-');
            $order['shipping_notes'] = $request->input('shipping_notes');

            $order['shipping_first_name'] = $request->input('shipping_first_name');
            $order['shipping_last_name'] = $request->input('shipping_last_name');
            $order['shipping_state'] = $request->input('shipping_state');
            $order['shipping_city'] = $request->input('shipping_city');
            $order['shipping_address'] = $request->input('shipping_address');
            $order['shipping_phone'] = $request->input('shipping_phone');

            if (!$request->has('billing_order')) {
                $order['billing_first_name'] = $request->input('shipping_first_name');
                $order['billing_last_name'] = $request->input('shipping_last_name');
                $order['billing_state'] = $request->input('shipping_state');
                $order['billing_city'] = $request->input('shipping_city');
                $order['billing_address'] = $request->input('shipping_address');
                $order['billing_phone'] = $request->input('shipping_phone');
            } else {
                $order['billing_first_name'] = $request->input('billing_first_name');
                $order['billing_last_name'] = $request->input('billing_last_name');
                $order['billing_state'] = $request->input('billing_state');
                $order['billing_city'] = $request->input('billing_city');
                $order['billing_address'] = $request->input('billing_address');
                $order['billing_phone'] = $request->input('billing_phone');
            }

            $order['grand_total'] = \Cart::session(auth()->id())->getTotal();
            $order['item_count'] = \Cart::session(auth()->id())->getContent()->count();

            $userId = auth()->id();
            $order['user_id'] = $userId;

            if (request('payment_method') == 'card') {
                $order['payment_method'] = 'card';
            }

            if (request('payment_method') == 'paypal') {
                $order['payment_method'] = 'paypal';
                return redirect()->route('paypal.checkout', $order->id);
            }

            $order->save();

            //save order items
            $cartItems = \Cart::session(auth()->id())->getContent();

            foreach ($cartItems as $item) {
                $order->items()->attach($item->id, ['price' => $item->price, 'quantity' => $item->quantity, 'user_id' => $userId]);
            }

            //payment
            if (request('payment_method') == 'card') {
                //redirect to card
                return redirect()->route('checkout.charge_request', $order->id);
            }

            //hyperpay
            if (request('payment_method') == 'hyperpay') {
                $trackable = [
                    'product_id'=> 'bc842310-371f-49d1-b479-ad4b387f6630',
                    'product_type' => 't-shirt'
                ];
                $user = auth()->user();
                $amount = 10;
                $brand = 'VISA'; // MASTER OR MADA
                $id = Str::random('64');
                return LaravelHyperpay::checkout($trackable, $user, $amount, $brand, $request);
                // return LaravelHyperpay::checkout($trackable_data, $user, $amount, $brand, $request);
                // return redirect()->route('checkout.hyperpaycheckout', $request);
            }

            //empty cart
            \Cart::session(auth()->id())->clear();

            Mail::to($request->user())->send(new OrderCompleted());

            //send email to customer
            return redirect()->route('home')->with([
                'message' => 'Order has been placed successfully',
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => 'Something was wrong, please try again',
            'alert-type' => 'danger'
        ]);
    }

    public function paymentStatus(Request $request)
    {
        $resourcePath = $request->get('resourcePath');
        $checkout_id = $request->get('id');
        return LaravelHyperpay::paymentStatus($resourcePath, $checkout_id);
    }
//    public function store(Request $request)
//    {
//        // Insert into orders table
//        $order = Order::create([
//            'user_id' => auth()->user()->id,
////            'date' => Carbon::now(),
//            'address' => $request['address'],
//            'status' => 0
//        ]);
//
//        // Insert into order items table
//        foreach (\Cart::session(auth()->id())->getContent() as $item) {
//            OrderItem::create([
//                'order_id' => $order->id,
//                'product_id' => $item->id,
//                'quantity' => $item->quantity,
//                'price' => $item->price
//            ]);
//
//            // payment
//            if ($request['payment_method'] == 'paypal') {
//
//                // Redirect to paypal
//                return redirect()->route('paypal.checkout', $order->id);
//            }
//
//            // Empty cart
////        \Cart::session(auth()->id())->remove($item['id']);
//            \Cart::session(auth()->id())->clear();
//        }
//
//        return redirect()->route('home')->with('msg', 'Order has been placed successfully');
//    }

}
