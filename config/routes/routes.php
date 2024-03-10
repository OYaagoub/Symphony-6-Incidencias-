<?php

use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\RegisterController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
     $routes->add('index', '/')
          ->controller([HomeController::class, 'index']);
      
    // ;
    // $routes->add('app_login', '/login')
    //     ->controller([loginController::class, 'login'])
    // ;
    $routes->add('registrar', '/register')
         ->controller([RegisterController::class, 'register'])
         
     ;
   
};