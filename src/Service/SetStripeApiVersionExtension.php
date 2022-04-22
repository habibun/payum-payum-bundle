<?php

namespace App\Service;

use FluxSE\PayumStripe\Action\Api\Resource\ResourceActionInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Stripe\Stripe;

class SetStripeApiVersionExtension implements ExtensionInterface
{
    private string $apiVersion;

    public function __construct(string $apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    /**
     * {@inheritDoc}
     */
    public function onPreExecute(Context $context)
    {

        $action = $context->getAction();
        // If it's not one of the Stripe API call
        if (false === $action instanceof ResourceActionInterface) {
            return;
        }

        Stripe::setApiVersion($this->apiVersion);
    }

    /**
     * {@inheritDoc}
     */
    public function onExecute(Context $context)
    {
//        Stripe::setApiVersion($this->apiVersion);
    }

    /**
     * {@inheritDoc}
     */
    public function onPostExecute(Context $context)
    {
//        Stripe::setApiVersion($this->apiVersion);
    }
}
