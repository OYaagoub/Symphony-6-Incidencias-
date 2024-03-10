<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Cliente;
use App\Entity\Incidencia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request as Rec;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class LoginController extends AbstractController
{
    private  $passwordHasher;
    private $entityManager;
    

    
    public function __construct(UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $entityManager)
    {
        
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils,AuthorizationCheckerInterface $authorizationChecker): Response
    {
        // // get the login error if there is one
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');
        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Redirect to the dashboard or any other route for authenticated users
            return $this->redirectToRoute('app_cliente');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        $this->addFlash('ss', '¡Se ha Cerrar La sission correctamente!');
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }



    #[Route(path: '/check', name: 'app_check')]
    public function check(Rec $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $data = json_decode($request->getContent(), true);

        // Ensure both email and password are provided in the request
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Get the currently logged-in user
        $userLogged = $this->getUser();
        $userLoggedData = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userLogged->getUserIdentifier()]);

        // Check if the provided email and password match the logged-in user
        if ($userLoggedData && $userLoggedData->getEmail() === $data['email'] &&  $this->passwordHasher->isPasswordValid($userLoggedData, $data['password'])) {
            //return $this->redirect($data['url']);
            $classes = ['C'=>Cliente::class,'I'=> Incidencia::class];

            $StringtoArray =explode(',', $data['path']);
            $className = $StringtoArray[0];
            $entity = $this->entityManager->getRepository($classes[$className])->findOneBy(['id' => $StringtoArray[1]]);

            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->addFlash('ss', '¡Se ha Borrado correctamente!');
            return new JsonResponse(['result' => true]);
            
        }else{

            return new JsonResponse(['result' => false]);
        }

    }

}
