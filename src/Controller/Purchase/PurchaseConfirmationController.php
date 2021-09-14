<?php

namespace App\Controller\Purchase;

use DateTime;
use DateTimeImmutable;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;



class PurchaseConfirmationController extends AbstractController
{
    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(EntityManagerInterface $em, Request $request, CartService $cartService)
    {


        // 1. Nous voulons lire les données du formulaire
        // FormFactoryInterface / Request
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : dégager
        if (!$form->isSubmitted()) {
            //Message flashes et redirection
            $this->addFlash('warning', "Fraude");
            return $this->redirectToRoute('cart_show');
        }

        // 3. Si je suis pas connecté : dégager
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException("Vous êtes pas connecté");
        }

        // 4. S'il n'y a pas de produit dans mon panier : dégager
        $cartItems = $cartService->getDetailedCartItems();
        if (count($cartItems) === 0) {
            $this->addFlash('warning', "panier vide");
            return $this->redirectToRoute('cart_show');
        }

        // 5. Nous allons créer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        // 6. Nous allons la lier avec l'utilisateur
        $purchase->setUser($user)
            ->setPurchasedAt(DateTimeImmutable::createFromMutable(new DateTime()))
            ->setTotal($cartService->getTotal());
        $em->persist($purchase);
        // 7. Nous allons la lier avec les produits

        foreach ($cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal());


            $em->persist($purchaseItem);
        }


        // 8. Nous allons enregistrer la commande
        $em->flush();


        $cartService->empty();

        $this->addFlash('success', "La commande a bien était enregistré");

        return $this->redirectToRoute('purchase_index', [
            'id' => $purchase->getId()
        ]);
    }
}
