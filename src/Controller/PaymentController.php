<?php

namespace App\Controller;

use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/payment", name="payment_")
 */
class PaymentController extends AbstractController
{
    /**
     * @Route("/prepare", name="prepare")
     */
    public function prepareAction(Payum $payum)
    {
//        $gatewayName = 'offline';
        $gatewayName = 'stripe_checkout_session';

        $storage = $payum->getStorage('App\Entity\Payment');

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('USD');
        $payment->setTotalAmount(123); // 1.23 USD
        $payment->setDescription('pay with dollar');
        $payment->setClientId('anId');
        $payment->setClientEmail('foo@example.com');

        $storage->update($payment);

        $captureToken = $payum->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'payment_done' // the route to redirect after capture
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * @Route("/done", name="done")
     */
    public function doneAction(Request $request, Payum $payum)
    {
        $token = $payum->getHttpRequestVerifier()->verify($request);

        $gateway = $payum->getGateway($token->getGatewayName());

        // You can invalidate the token, so that the URL cannot be requested any more:
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);

        // Once you have the token, you can get the payment entity from the storage directly.
        // $identity = $token->getDetails();
        // $payment = $this->get('payum')->getStorage($identity->getClass())->find($identity);

        // Or Payum can fetch the entity for you while executing a request (preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();

        // Now you have order and payment status

        return new JsonResponse([
            'status' => $status->getValue(),
            'payment' => [
                'total_amount' => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details' => $payment->getDetails(),
            ],
        ]);
    }

    /**
     * @Route("/webhook", name="webhook")
     */
    public function webhookAction(Request $request)
    {
        $type = $request->request->get('type');
        switch ($type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $request->request->get('data')['object']['amount'];
                echo 'PaymentIntent for '.$paymentIntent.' is succeeded!';
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $request->request->get('data')['object']['amount'];
                echo 'PaymentIntent for '.$paymentIntent.' is failed!';
                break;

            default:
                echo 'Received unknown event type';
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        return $response->send();
    }
}
