<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{
    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="vous devez être connecté")
     */
    public function index(Security $security)
    {
        // 1. Nous devons nous assurer que la personne est connectée (sinon page d'accueil) -> secutiry 
        /** @var User */
        $user = $this->getUser();

        // 2. Nous voulons savoir QUI est connecté ->secutrity 

        // 3. Nous voulons passer l'utilisateur connecté a twwig afin d'afficher ces commandes -> Environement de twig / response
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}
