<?php
namespace PayumPaypal;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use PayumPaypal\Action\Api\ConfirmOrderAction;
use PayumPaypal\Action\Api\CreateRecurringPaymentProfileAction;
use PayumPaypal\Action\Api\DoCaptureAction;
use PayumPaypal\Action\Api\DoExpressCheckoutPaymentAction;
use PayumPaypal\Action\Api\GetExpressCheckoutDetailsAction;
use PayumPaypal\Action\Api\GetRecurringPaymentsProfileDetailsAction;
use PayumPaypal\Action\Api\GetTransactionDetailsAction;
use PayumPaypal\Action\Api\SetExpressCheckoutAction;
use PayumPaypal\Action\Api\AuthorizeTokenAction;
use PayumPaypal\Action\Api\CancelRecurringPaymentsProfileAction;
use PayumPaypal\Action\Api\ManageRecurringPaymentsProfileStatusAction;
use PayumPaypal\Action\Api\CreateBillingAgreementAction;
use PayumPaypal\Action\Api\DoReferenceTransactionAction;
use PayumPaypal\Action\Api\UpdateRecurringPaymentProfileAction;
use PayumPaypal\Action\AuthorizeAction;
use PayumPaypal\Action\CaptureAction;
use PayumPaypal\Action\ConvertPaymentAction;
use PayumPaypal\Action\NotifyAction;
use PayumPaypal\Action\PaymentDetailsStatusAction;
use PayumPaypal\Action\PaymentDetailsSyncAction;
use PayumPaypal\Action\RecurringPaymentDetailsStatusAction;
use PayumPaypal\Action\RecurringPaymentDetailsSyncAction;

class PaypalExpressCheckoutGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults(array(
            'payum.factory_name' => 'paypal_express_checkout_nvp',
            'payum.factory_title' => 'PayPal ExpressCheckout',

            'payum.template.confirm_order' => '@PayumPaypalExpressCheckout/confirmOrder.html.twig',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new PaymentDetailsStatusAction(),
            'payum.action.sync' => new PaymentDetailsSyncAction(),
            'payum.action.recurring_status' => new RecurringPaymentDetailsStatusAction(),
            'payum.action.recurring_sync' => new RecurringPaymentDetailsSyncAction(),

            'payum.action.api.set_express_checkout' => new SetExpressCheckoutAction(),
            'payum.action.api.get_express_checkout_details' => new GetExpressCheckoutDetailsAction(),
            'payum.action.api.get_transaction_details' => new GetTransactionDetailsAction(),
            'payum.action.api.do_express_checkout_payment' => new DoExpressCheckoutPaymentAction(),
            'payum.action.api.create_recurring_payment_profile' => new CreateRecurringPaymentProfileAction(),
            'payum.action.api.update_recurring_payment_profile' => new UpdateRecurringPaymentProfileAction(),
            'payum.action.api.get_recurring_payments_profile_details' => new GetRecurringPaymentsProfileDetailsAction(),
            'payum.action.api.cancel_recurring_payments_profile' => new CancelRecurringPaymentsProfileAction(),
            'payum.action.api.manage_recurring_payments_profile_status' => new ManageRecurringPaymentsProfileStatusAction(),
            'payum.action.api.create_billing_agreement' => new CreateBillingAgreementAction(),
            'payum.action.api.do_reference_transaction' => new DoReferenceTransactionAction(),
            'payum.action.api.do_capture' => new DoCaptureAction(),
            'payum.action.api.authorize_token' => new AuthorizeTokenAction(),
            'payum.action.api.confirm_order' => function (ArrayObject $config) {
                return new ConfirmOrderAction($config['payum.template.confirm_order']);
            },
        ));

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'username' => '',
                'password' => '',
                'signature' => '',
                'sandbox' => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = array('username', 'password', 'signature');

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $paypalConfig = array(
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'signature' => $config['signature'],
                    'sandbox' => $config['sandbox'],
                );

                return  new Api($paypalConfig, $config['payum.http_client']);
            };
        }

        $config['payum.paths'] = array_replace([
            'PayumPaypalExpressCheckout' => __DIR__.'/Resources/views',
        ], $config['payum.paths'] ?: []);
    }
}
