<?php
use Illuminate\Http\Request;
use Qteco\MolliePayments\Classes\MolliePayment;

Route::post('/molliepayments-checkout', function (Request $request) {
    $molliePayment = new MolliePayment;
    $molliePayment->changePaymentState($request);

    return exit();
});
