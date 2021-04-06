<?php
namespace Qteco\MolliePayments\Classes;

use OFFLINE\Mall\Classes\Payments\PaymentProvider;
use OFFLINE\Mall\Classes\Payments\PaymentResult;
use OFFLINE\Mall\Models\PaymentGatewaySettings;
use Omnipay\Omnipay;
use Throwable;
use Session;
use Lang;

class MolliePayment extends PaymentProvider
{
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
        $response = null;

        try {
            $response = $this->getGateway()->purchase([
                'amount' => $this->order->total_in_currency,
                'currency' => $this->order->currency['code'],
                'returnUrl' => $this->returnUrl(),
                'description' => Lang::get('qteco.molliepayments::lang.messages.order_number') . $this->order->order_number,
            ])->send();
        } catch (Throwable $e) {
            return $result->fail([], $e);
        }

        if (!$response->isRedirect()) {
            return $result->fail((array)$response->getData(), $response);
        }

        Session::put('mall.payment.callback', self::class);
        Session::put('qteco.molliepayments.transactionReference', $response->getTransactionReference());

        $this->setOrder($result->order);
        $result->order->payment_transaction_id = $response->getTransactionReference();
        $result->order->save();

        return $result->redirect($response->getRedirectResponse()->getTargetUrl());
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
        $transactionReference = Session::pull('qteco.molliepayments.transactionReference');

        if (!$transactionReference) {
            return $result->fail([
                'msg' => 'Missing transaction reference'
            ], null);
        }

        try {
            $response = $this->getGateway()->completePurchase([
                'transactionReference' => $transactionReference
            ])->send();
        } catch (Throwable $e) {
            return $result->fail([], $e);
        }

        if (!$response->isSuccessful()) {
            return $result->fail((array)$result, null);
        }

        return $result->success((array)$result, $response);
    }

    /**
     * Build the Omnipay Gateway for Mollie.
     *
     * @return \Omnipay\Common\GatewayInterface
     */
    protected function getGateway()
    {
        $apiKey = null;

        if (PaymentGatewaySettings::get('mollie_mode') == 'live') {
            $apiKey = PaymentGatewaySettings::get('live_api_key');
        } else {
            $apiKey = PaymentGatewaySettings::get('test_api_key');
        }

        $gateway = Omnipay::create('Mollie');
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
