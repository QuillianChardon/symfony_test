<?php

namespace App\Controller\Purchase;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     */
    public function showCardForm($id, PurchaseRepository $purchaseRepository)
    {
        $purchase = $purchaseRepository->find($id);
        if (!$purchase) {
            return $this->redirectToRoute('cart_show');
        }

        Stripe::setApiKey('sk_test_51JXkAjE278rdKKrbl8WqpueGTsa9XPv28uFF14cGGGAmnxQ55woASjWCfcCc9P02K4oZwbvDRlGDnPI3w5BhwKhU00xO2mlsms');

        $intent = PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur'
        ]);
        //dd($intent);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret
        ]);
    }
}
