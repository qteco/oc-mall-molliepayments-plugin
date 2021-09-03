# Mollie Payments for Mall

Mollie payment provider for the [OFFLINE.Mall](https://github.com/OFFLINE-GmbH/oc-mall-plugin) plugin for October CMS.

This plugin adds a [Mollie](https://www.mollie.com/en) payment provider to the [OFFLINE.Mall](https://octobercms.com/plugin/offline-mall) plugin. It uses the [Mollie API client for PHP](https://github.com/mollie/mollie-api-php) to process payments.

The following Mollie payment methods are supported:

-   Apple Pay
-   Bancontact
-   Bank transfer
-   Belfius
-   Credit card
-   SEPA Direct Debit
-   EPS
-   Gift card
-   Giropay
-   iDEAL
-   KBC/CBC
-   MyBank
-   PayPal
-   Paysafecard
-   Przelewy24
-   SOFORT

## Installing the plugin

If you are using October CMS v1.0.x or v1.1.x, you will need to install the composer dependencies manually.
After installing the plugin, run `composer install` in `plugins/qteco/mallmolliepayments`.

## Using the payment gateway in your store

1. Create a [Mollie](https://www.mollie.com/en) account and get your test and live API keys
2. Install this plugin
3. Enter your API keys in Settings -> Mall: Payments -> Payment gateways -> Mollie
4. Set the orders page, this is the page that users are redirected to after completing the checkout.

## Creating payment methods using the Mollie gateway

After setting up your Mollie gateway, you can create payment methods that utilize the Mollie payment gateway. When creating payment methods, make sure to enter the correct value in the **Code** field. Here are the codes for all of the supported payment methods:

**Note:** This list was last updated on 12-05-2021 (d-m-y), please always check the [Mollie documentation](https://docs.mollie.com/reference/v2/payments-api/create-payment) for an up-to-date overview of supported payment methods. You can find the supported payment methods under **Parameters** in the `methods` section.

| Name              | Code         |
| ----------------- | ------------ |
| Apple Pay         | applepay     |
| Bancontact        | bancontact   |
| Bank transfer     | banktransfer |
| Belfius           | belfius      |
| Credit card       | creditcard   |
| SEPA Direct Debit | directdebit  |
| EPS               | eps          |
| Gift card         | giftcard     |
| Giropay           | giropay      |
| iDEAL             | ideal        |
| KBC/CBC           | kbc          |
| MyBank            | mybank       |
| PayPal            | paypal       |
| Paysafecard       | paysafecard  |
| Przelewy24        | przelewy24   |
| SOFORT            | sofort       |

## Test vs. live mode

Test mode will use the test API key and is used to test the creation of orders before you deploy your store. Live mode will use the live API key and will process real payments.
