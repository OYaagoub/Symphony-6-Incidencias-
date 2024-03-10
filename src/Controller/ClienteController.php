<?php

namespace App\Controller;

use App\Entity\Cliente;

use App\Form\ClienteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ClienteController extends AbstractController

{

    private $entityManager;
  
    public function __construct(EntityManagerInterface $entityManager)
    {
        
        $this->entityManager = $entityManager;
    
    }


    #[Route('/clientes/nuevo', name: 'crear_cliente')]
    public function crearCliente(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        // Crear una nueva instancia de la entidad Cliente
        $cliente = new Cliente();

        // Crear un formulario para el cliente
        $form = $this->createForm(ClienteFormType::class, $cliente);

        // Manejar la solicitud del formulario
        $form->handleRequest($request);

        // Verificar si el formulario ha sido enviado y es válido
        if ($form->isSubmitted() && $form->isValid()) {
            // Persistir el cliente en la base de datos
            
            $this->entityManager->persist($cliente);
            $this->entityManager->flush();
            $this->addFlash('ss', '¡Se ha insertado correctamente!');
            // Redirigir a la lista de clientes
            return $this->redirectToRoute('app_cliente');
        }
        // Renderizar la plantilla con el formulario
        return $this->render('cliente/crear_cliente.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    
    #[Route('/clientes', name: 'app_cliente')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $tuEntidadRepository = $this->entityManager->getRepository(Cliente::class);
        $clientes = $tuEntidadRepository->findAll();

        return $this->render('cliente/index.html.twig', [
            'clientes' => $clientes,
        ]);
    }

    #[Route('/clientes/{id}', name: 'ver_cliente')]
    public function verCliente(Cliente $cliente): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->render('/cliente/ver_cliente.html.twig', [
            'cliente' => $cliente,
        ]);
    }

    #[Route('/clientes/{id}/borrar', name: 'borrar_cliente')]
    public function borrarCliente(Cliente $cliente): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        // Usar el EntityManager para eliminar el cliente
        $this->entityManager->remove($cliente);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_cliente');
    }


    
    
}
