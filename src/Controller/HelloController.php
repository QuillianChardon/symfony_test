<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    protected $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     *@Route("/hello/{prenom?World}", name="hello")
     */
    public function index($prenom, LoggerInterface $logger, Slugify $slugify)
    {
        dump($slugify->slugify("Hello world"));
        $logger->error("Mon message de log");
        $tva = $this->calculator->calcul(100);
        dump($tva);
        return new Response("Hello $prenom");
    }
}
