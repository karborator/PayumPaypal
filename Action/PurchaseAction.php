<?php
namespace PayumPaypal\Action;

use League\Url\Url;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Sync;
use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use PayumPaypal\Request\Api\ConfirmOrder;
use PayumPaypal\Request\Api\SetExpressCheckout;
use PayumPaypal\Request\Api\AuthorizeToken;
use PayumPaypal\Request\Api\DoExpressCheckoutPayment;
use PayumPaypal\Api;

abstract class PurchaseAction extends GatewayAwareAction implements GenericTokenFactoryAwareInterface
{
    /**
     * @var GenericTokenFactoryInterface
     */
    protected $tokenFactory;

    /**
     * @param GenericTokenFactoryInterface $genericTokenFactory
     *
     * @return void
     */
    public function setGenericTokenFactory(GenericTokenFactoryInterface $genericTokenFactory = null)
    {
        $this->tokenFactory = $genericTokenFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request Capture */
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $details->validateNotEmpty('PAYMENTREQUEST_0_PAYMENTACTION');

        $details->defaults(array(
            'AUTHORIZE_TOKEN_USERACTION' => Api::USERACTION_COMMIT,
        ));

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if (isset($httpRequest->query['cancelled'])) {
            $details['CANCELLED'] = true;

            return;
        }

        if (false == $details['TOKEN']) {

            if (false == $details['RETURNURL'] && $request->getToken()) {
                $details['RETURNURL'] = $request->getToken()->getTargetUrl();
            }

            if (false == $details['CANCELURL'] && $request->getToken()) {
                $details['CANCELURL'] = $request->getToken()->getTargetUrl();
            }

            if (empty($details['PAYMENTREQUEST_0_NOTIFYURL']) && $request->getToken() && $this->tokenFactory) {
                $notifyToken = $this->tokenFactory->createNotifyToken(
                    $request->getToken()->getGatewayName(),
                    $request->getToken()->getDetails()
                );

                $details['PAYMENTREQUEST_0_NOTIFYURL'] = $notifyToken->getTargetUrl();
            }

            if ($details['CANCELURL']) {
                $cancelUrl = Url::createFromUrl($details['CANCELURL']);
                $query = $cancelUrl->getQuery();
                $query->modify(['cancelled' => 1]);
                $cancelUrl->setQuery($query);

                $details['CANCELURL'] = (string) $cancelUrl;
            }

            $this->gateway->execute(new SetExpressCheckout($details));

            if (isset($details['L_ERRORCODE0'])) {
                throw new \Exception($details['L_ERRORCODE0']);
            }
        }

        //TODO
        if(isset($details['TOKEN'])){
            $request->getToken()->setAfterUrl(
                'https://www.sandbox.paypal.com/ca/cgi-bin/merchantpaymentweb?cmd=_express-checkout&token='
                . $details['TOKEN'] .
                '#/checkout/review'
            );
        }
    }
}
