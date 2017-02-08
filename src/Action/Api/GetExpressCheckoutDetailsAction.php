<?php
namespace PayumPaypal\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\LogicException;
use PayumPaypal\Request\Api\GetExpressCheckoutDetails;

class GetExpressCheckoutDetailsAction extends  BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request GetExpressCheckoutDetails */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        if (false == $model['TOKEN']) {
            throw new LogicException('TOKEN must be set. Have you run SetExpressCheckoutAction?');
        }

        $model->replace(
            $this->api->getExpressCheckoutDetails(array('TOKEN' => $model['TOKEN']))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetExpressCheckoutDetails &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
