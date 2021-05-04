<?php
namespace Qteco\MolliePayments\Classes;

use OFFLINE\Mall\Classes\Payments\PaymentProvider;
use OFFLINE\Mall\Classes\Payments\PaymentResult;
use OFFLINE\Mall\Models\PaymentGatewaySettings;
use OFFLINE\Mall\Models\OrderState;
use OFFLINE\Mall\Models\Order;
use Throwable;
use Session;
use Lang;
use Illuminate\Support\Facades\Log;

class MolliePayment extends PaymentProvider
{
    /**
     * The order that is being paid.
     *
     * @var \OFFLINE\Mall\Models\Order
     */
    public $order;
    /**
     * Data that is needed for the payment.
     * Card numbers, tokens, etc.
     *
     * @var array
     */
    public $data;

    /**
     * Return the display name of your payment provider.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Mollie';
    }

    /**
     * Return a unique identifier for this payment provider.
     *
     * @return string
     */
    public function identifier(): string
    {
        return 'mollie';
    }

    /**
     * Validate the given input data for this payment.
     *
     * @return bool
     *
     * @throws \October\Rain\Exception\ValidationException
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Process the payment.
     *
     * @param PaymentResult $result
     *
     * @return PaymentResult
     */
    public function process(PaymentResult $result): PaymentResult
    {
        $payment = null;

        try {
            $payment = $this->getGateway()->payments->create([
                "amount" => [
                    "currency" => $this->order->currency['code'],
                    //"value" => $this->order->total_in_currency,
                    "value" => "10.00"
                ],
                "description" => Lang::get('qteco.molliepayments::lang.messages.order_number') . $this->order->order_number,
                "redirectUrl" => $this->returnUrl(),
                "webhookUrl"  => "https://malltest.qteco.nl/molliepayments-checkout",
            ]);
        } catch (Throwable $e) {
            return $result->fail([], $e);
        }

        Session::put('mall.payment.callback', self::class);
        Session::put('qteco.molliepayments.transactionReference', $payment->id);

        $this->setOrder($result->order);
        $result->order->payment_transaction_id = $payment->id;
        $result->order->save();

        return $result->redirect($payment->getCheckoutUrl());
    }

    /**
     * Mollie has processed the payment and has redirected the user back to the website.
     *
     * @param PaymentResult $result
     *
     * @return PaymentResult
     */
    public function complete(PaymentResult $result): PaymentResult
    {
        return $result->success((array)$result, 'payment succeeded');
    }

    public function changePaymentState($response)
    {
        Log::info('Mollie transaction id: ' . $response->id);

        $order = Order::where('payment_transaction_id', $response->id)->first();

        $this->setOrder($order);
        Log::info('order id: ' . $order->id);

        $result = new PaymentResult($this, $order);

        try {
            $payment = $this->getGateway()->payments->get($response->id);
        } catch (Throwable $e) {
            return $result->fail([], $e);
        }

        $errorMessage = '';

        Log::info('payment status: ' . $payment->status);

        switch ($payment->status) {
            case 'paid':
                try {
                    \Event::fire('mall.checkout.succeeded', $result);
                    Log::info('fired checkout event');
                } catch (Throwable $e) {
                    Log::info('didnt fire checkout event');
                    return null;
                }

                return $result->success((array)$payment, $payment->id);
            case 'expired':
                $errorMessage = Lang::get('qteco.molliepayments::lang.messages.payment_expired');
                return $result->fail((array)$payment, $errorMessage);
            case 'failed':
                $errorMessage = Lang::get('qteco.molliepayments::lang.messages.payment_failed');
                return $result->fail((array)$payment, $errorMessage);
            case 'canceled':
                $order->save();

                return $result->fail((array)$payment, $errorMessage);
            default:
                return $result->fail((array)$payment, 'payment failed for unknown reason');
        }
    }

    /**
     * Build the payment gateway for Mollie.
     *
     * @return \Mollie\Api\MollieApiClient
     */
    protected function getGateway()
    {
        $apiKey = null;

        if (PaymentGatewaySettings::get('mollie_mode') == 'live') {
            $apiKey = PaymentGatewaySettings::get('live_api_key');
        } else {
            $apiKey = PaymentGatewaySettings::get('test_api_key');
        }

        $gateway = new \Mollie\Api\MollieApiClient();
        $gateway->setApiKey(decrypt($apiKey));

        return $gateway;
    }

    /**
     * Return any custom backend settings fields.
     *
     * These fields will be rendered in the backend
     * settings page of your provider.
     *
     * @return array
     */
    public function settings(): array
    {
        return [
            'mollie_mode' => [
                'label' => 'qteco.molliepayments::lang.settings.mollie_mode',
                'default' => 'test',
                'comment' => 'qteco.molliepayments::lang.settings.mollie_mode_label',
                'span' => 'left',
                'type' => 'dropdown',
                'options' => [
                    'test' => 'Test',
                    'live' => 'Live',
                ],
            ],
            'test_api_key' => [
                'label' => 'qteco.molliepayments::lang.settings.test_api_key',
                'comment' => 'qteco.molliepayments::lang.settings.test_api_key_label',
                'span' => 'left',
                'type' => 'text',
            ],
            'live_api_key' => [
                'label' => 'qteco.molliepayments::lang.settings.live_api_key',
                'comment' => 'qteco.molliepayments::lang.settings.live_api_key_label',
                'span' => 'left',
                'type' => 'text',
            ],
            'orders_page' => [
                'label' => 'Orders page',
                'comment' => 'Example: http://yourwebsite.com/account/orders',
                'span' => 'left',
                'type' => 'text',
            ],
        ];
    }

    /**
     * Setting keys returned from this method are stored encrypted.
     *
     * Use this to store API tokens and other secret data
     * that is needed for this PaymentProvider to work.
     *
     * @return array
     */
    public function encryptedSettings(): array
    {
        return [
            'test_api_key',
            'live_api_key',
        ];
    }
}
