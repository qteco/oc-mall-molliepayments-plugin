<?php
namespace Qteco\MallMolliePayments;

use OFFLINE\Mall\Classes\Payments\PaymentGateway;
use Qteco\MallMolliePayments\Classes\MolliePayment;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['Offline.Mall'];

    public function pluginDetails()
    {
        return [
            'name'        => 'qteco.mallmolliepayments::lang.plugin.name',
            'description' => 'qteco.mallmolliepayments::lang.plugin.description',
            'author'      => 'Qteco B.V.',
            'icon'        => 'icon-money',
            'homepage'    => 'https://github.com/Qteco/oc-mall-molliepayments-plugin'
        ];
    }

    public function boot()
    {
        $gateway = $this->app->get(PaymentGateway::class);
        $gateway->registerProvider(new MolliePayment());
    }
}
