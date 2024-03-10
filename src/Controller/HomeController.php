<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
   
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->redirectToRoute('app_cliente');
    }
}
