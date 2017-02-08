<?php
namespace PayumPaypal\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use PayumPaypal\Request\Api\CreateRecurringPaymentProfile;
use PayumPaypal\Request\Api\GetRecurringPaymentsProfileDetails;

class GetRecurringPaymentsProfileDetailsAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request CreateRecurringPaymentProfile */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $model->validateNotEmpty('PROFILEID');

        $model->replace(
            $this->api->getRecurringPaymentsProfileDetails(array('PROFILEID' => $model['PROFILEID']))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetRecurringPaymentsProfileDetails &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
