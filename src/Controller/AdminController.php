<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_index")
     */
    public function index()
    {
        $liens = [
            ['homepage', "page d'accueil"],
            ['admin_statistique', "page statistique"]
        ];

        return $this->render('admin/index.html.twig', [
            'liens' => $liens
        ]);
    }

    /**
     * @Route("/admin/statistique", name="admin_statistique")
     */
    public function statistique(ProductRepository $productRepository, CategoryRepository $categoryRepository, UserRepository $userRepository, PurchaseRepository $purchaseRepository)
    {
        //produit
        $nbProduct = $productRepository->count([]);
        $products = $productRepository->findAll();


        $nbCategory = $categoryRepository->count([]);
        $categories = $categoryRepository->findAll();

        $nbProductByCategory = [];

        foreach ($categories as $category) {
            $nbProductByCategory[$category->getId()] = $category->getProducts()->count();
        }


        $userRole = [];

        $nbUser = $userRepository->count([]);
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            $userRole[$user->getId()] = $user->getRoles();
        }


        $nbOrder = $purchaseRepository->count([]);
        $orders = $purchaseRepository->findAll();



        return $this->render('admin/statistique.html.twig', [
            //product
            'nbProduct' => $nbProduct,
            'products' => $products,
            //category
            'nbCategory' => $nbCategory,
            'categories' => $categories,
            'nbProductByCategory' => $nbProductByCategory,
            //user
            'nbUser' => $nbUser,
            'users' => $users,
            'usersRole' => $userRole,
            //order
            'nbOrder' => $nbOrder,
            'orders' => $orders,
        ]);
    }
}
