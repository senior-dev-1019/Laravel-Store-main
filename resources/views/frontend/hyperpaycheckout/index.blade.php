@extends('frontend.layouts.app')

<script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId=uat01-vm-tx04"></script>

@section('content')
    <form
	    action="/hyperpay/finalize"
	    class="paymentWidgets"
	    data-brands="VISA MASTER"
	></form>

@endsection
