<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LuckyController extends AbstractController {
    #[Route('/nb')]
    public function number():Response{
        $nb = random_int(0,100);
        return $this->render('lucky/number.html.twig',['nb'=>$nb,]);
    }
}