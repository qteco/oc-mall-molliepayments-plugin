<?php
namespace Qteco\MallMolliePayments\Classes;

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

        $webhookUrl = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        $webhookUrl .= 'oc-mall-molliepayments-checkout';

        try {
            $payment = $this->getGateway()->payments->create([
                "amount" => [
                    "currency" => $this->order->currency['code'],
                    //"value" => $this->order->total_in_currency,
                    "value" => "10.00"
                ],
                "description" => Lang::get('qteco.mallmolliepayments::lang.messages.order_number') . $this->order->order_number,
                "redirectUrl" => $this->returnUrl(),
                "webhookUrl" => $webhookUrl,
                //"method" => "ideal",
                "metadata" => [
                    "order_id" => $this->order->order_number,
                ],
            ]);
        } catch (Throwable $e) {
            return $result->fail([], $e);
        }

        Session::put('mall.payment.callback', self::class);

        return $result->redirect($payment->getCheckoutUrl());
    }

    public function complete(PaymentResult $result): PaymentResult
    {
        return $result->redirect(PaymentGatewaySettings::get('orders_page'));
    }

    /**
     * Mollie has processed the payment and has redirected the user back to the website.
     *
     * @param mixed $response
     *
     * @return PaymentResult
     */
    public function changePaymentStatus($response): PaymentResult
    {
        $payment = $this->getGateway()->payments->get($response->id);

        $order = Order::where('id', $payment->metadata->order_id)->first();

        $this->setOrder($order);

        $result = new PaymentResult($this, $order);

        $message = '';

        Log::info('payment status: ' . $payment->status);

        switch ($payment->status) {
            case 'paid':
                $message = Lang::get('qteco.mallmolliepayments::lang.messages.payment_paid');
                return $result->success((array)$payment, $message);
            case 'expired':
                $message = Lang::get('qteco.mallmolliepayments::lang.messages.payment_expired');
                return $result->fail((array)$payment, $message);
            case 'failed':
                $message = Lang::get('qteco.mallmolliepayments::lang.messages.payment_failed');
                return $result->fail((array)$payment, $message);
            case 'canceled':
                $message = Lang::get('qteco.mallmolliepayments::lang.messages.payment_canceled');
                $order->order_state_id = $this->getOrderStateId(OrderState::FLAG_CANCELLED);
                $order->save();
                return $result->fail((array)$payment, $message);
            default:
                $message = Lang::get('qteco.mallmolliepayments::lang.messages.payment_failed');
                return $result->fail((array)$payment, $message);
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
                'label' => 'qteco.mallmolliepayments::lang.settings.mollie_mode',
                'default' => 'test',
                'comment' => 'qteco.mallmolliepayments::lang.settings.mollie_mode_label',
                'span' => 'left',
                'type' => 'dropdown',
                'options' => [
                    'test' => 'Test',
                    'live' => 'Live',
                ],
            ],
            'test_api_key' => [
                'label' => 'qteco.mallmolliepayments::lang.settings.test_api_key',
                'comment' => 'qteco.mallmolliepayments::lang.settings.test_api_key_label',
                'span' => 'left',
                'type' => 'text',
            ],
            'live_api_key' => [
                'label' => 'qteco.mallmolliepayments::lang.settings.live_api_key',
                'comment' => 'qteco.mallmolliepayments::lang.settings.live_api_key_label',
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

    /**
     * Getting order state id by flag
     *
     * @param $orderStateFlag
     * @return int
     */
    protected function getOrderStateId($orderStateFlag): int
    {
        $orderStateModel = OrderState::where('flag', $orderStateFlag)->first();

        return $orderStateModel->id;
    }
}
