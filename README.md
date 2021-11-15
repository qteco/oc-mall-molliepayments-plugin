# Mollie iDEAL Payments for Mall

Mollie payment provider for iDEAL payments for the [OFFLINE.Mall](https://github.com/OFFLINE-GmbH/oc-mall-plugin) plugin for October CMS.

This plugin adds a [Mollie](https://www.mollie.com/en) payment provider to the [OFFLINE.Mall](https://octobercms.com/plugin/offline-mall) plugin. It uses the [Mollie API client for PHP](https://github.com/mollie/mollie-api-php) to process payments.

At the moment, only support for iDEAL payments is guaranteed. Your mileage may vary when using other payment methods.

## Installation

Install through the marketplace or by running `php artisan plugin:install Qteco.MallMolliePayments`
After installing the plugin, go to `plugins/qteco/mallmolliepayments` and run `composer install` to install the composer dependencies.

## Usage

1. Create a [Mollie](https://www.mollie.com/en) account and get your test and live API keys
2. Install this plugin
3. Enter your API keys in Settings -> Mall: Payments -> Payment gateways -> Mollie
4. Create a payment method utilizing the Mollie payment gateway, and enter `ideal` in the **code** field.

## Test vs. live mode

Test mode will use the test API key and is used to test the creation of orders before you deploy your store. Live mode will use the live API key and will process real payments.
