<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, ProductRepository $productRepository, CartService $cartService, Request $request)
    {

        $produit = $productRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        $cartService->add($id);

        $this->addFlash('success', "Le produit a bien été ajouté au panier");


        if ($request->query->get("returnToCart")) {
            return $this->redirectToRoute("cart_show");
        }
        return $this->redirectToRoute('product_show', [
            'category_slug' => $produit->getCategory()->getSlug(),
            'slug' => $produit->getSlug()
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(CartService $cartService)
    {

        $form = $this->createForm(CartConfirmationType::class);

        $detailCart = $cartService->getDetailedCartItems();
        $total = $cartService->getTotal();
        return $this->render('cart/index.html.twig', [
            'items' => $detailCart,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/cart/delet/{id}", name="cart_delet", requirements={"id":"\d+"})
     */
    public function delet($id, ProductRepository $productRepository, CartService $cartService)
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Produit non trouvé");
        }
        $cartService->remove($id);
        $this->addFlash('success', "Produit bien supprimer du panier");
        return $this->redirectToRoute("cart_show");
    }


    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id":"\d+"})
     */
    public function decrement($id, productRepository $productRepository, CartService $cartService)
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Produit non trouvé");
        }

        $this->addFlash('success', "Le produit a été décrémenté");
        $cartService->decrement($id);

        return $this->redirectToRoute("cart_show");
    }
}
