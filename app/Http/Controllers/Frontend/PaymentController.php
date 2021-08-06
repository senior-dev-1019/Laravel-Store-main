<?php 

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Devinweb\LaravelHyperpay\Facades\LaravelHyperpay;
use App\Billing\HyperPayBilling;
use Illuminate\Support\Str;

class PaymentController extends  Controller
{
    public function index()
    {
        // $cartItems = \Cart::session(auth()->id())->getContent();

        return view('frontend.hyperpaycheckout.index');

    }
    public function prepareCheckout(Request $request)
    {
        $trackable = [
            'product_id'=> 'bc842310-371f-49d1-b479-ad4b387f6630',
            'product_type' => 't-shirt'
        ];
        $user = User::first();
        $amount = 10;
        $brand = 'VISA'; // MASTER OR MADA
        $id = Str::random('64');
		return LaravelHyperpay::addMerchantTransactionId($id)->addBilling(new HyperPayBilling())->checkout($trackable_data, $user, $amount, $brand, $request);
        // return LaravelHyperpay::checkout($trackable_data, $user, $amount, $brand, $request);
    }

    public function paymentStatus(Request $request)
    {
        $resourcePath = $request->get('resourcePath');
        $checkout_id = $request->get('id');
        return LaravelHyperpay::paymentStatus($resourcePath, $checkout_id);
    }
}
