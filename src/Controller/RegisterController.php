<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RegisterController extends AbstractController
{


    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function register(Request $request, EntityManagerInterface $entityManager,AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Redirect to the dashboard or any other route for authenticated users
            return $this->redirectToRoute('app_cliente');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $fotoFile */
            $fotoFile = $form['foto']->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            if ($fotoFile) {
                $newFilename = md5(uniqid()) . '.' . $fotoFile->guessExtension();

                // Move the file to the desired directory
                $fotoFile->move(
                    $this->getParameter('your_foto_directory'), // Configure this parameter in your services.yaml file
                    $newFilename
                );

                // Update the User entity with the filename
                $user->setFoto($newFilename);
                $user->setRol("ROLE_USER");
            }
            $entityManager->persist($user);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            // ... (Existing user creation logic)
            $this->addFlash('ss', '¡Se ha registrado correctamente!');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
