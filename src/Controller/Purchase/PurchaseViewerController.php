<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseViewerController extends AbstractController
{

    /**
     * @Route("/purchase/view/{id}",name="view_purchase")
     */
    public function viewer($id, PurchaseRepository $purchaseRepository)
    {
        $purchase = $purchaseRepository->find($id);
        return $this->render('purchase/viewer.html.twig', [
            'purchase' => $purchase
        ]);
    }


    /**
     * @Route("/purchase/recommand/{id}", name="recommand_purchase")
     */
    public function recommand($id, PurchaseRepository $purchaseRepository, CartService $cartService)
    {
        $purchase = $purchaseRepository->find($id);
        foreach ($purchase->getPurchaseItems() as $item) {
            $cartService->addQtt($item->getProduct()->getId(), $item->getQuantity());
        }
        $form = $this->createForm(CartConfirmationType::class);

        $detailCart = $cartService->getDetailedCartItems();
        $total = $cartService->getTotal();
        return $this->render('cart/index.html.twig', [
            'items' => $detailCart,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);
    }
}
