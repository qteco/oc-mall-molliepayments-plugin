<?php
use Illuminate\Http\Request;
use Qteco\MallMolliePayments\Classes\MolliePayment;

Route::post("/oc-mall-molliepayments-checkout", function (Request $request) {
    $molliePayment = new MolliePayment();
    $molliePayment->changePaymentStatus($request);

    return exit();
});
