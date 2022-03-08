<?php

namespace App\Controller;

use App\Entity\PurchaseItem;
use App\Repository\ProductRepository;
use App\Repository\PurchaseItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/",name="homepage")
     */
    public function homepage(ProductRepository $productRepository, EntityManagerInterface $em, PurchaseItemRepository $purchaseItemRepository)
    {
        $arrayPrdct = [];
        foreach ($purchaseItemRepository->findAll() as $item) {
            //dd($item);
            if (array_key_exists($item->getProduct()->getId(), $arrayPrdct)) {
                $arrayPrdct[$item->getProduct()->getId()] += $item->getQuantity();
            } else {
                $arrayPrdct[$item->getProduct()->getId()] = $item->getQuantity();
            }
        }
        arsort($arrayPrdct);
        $cpt = 0;
        $products = [];
        foreach ($arrayPrdct as $key => $value) {
            if ($cpt < 3) {
                $products[$cpt] = $productRepository->find($key);
                $cpt++;
            }
        }

        return $this->render('home.html.twig', [
            'products' => $products
        ]);
    }
}
