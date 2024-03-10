<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security as Sec;

use App\Entity\Cliente;
use App\Entity\Incidencia;
use App\Form\IncidenciaFormType;
use App\Form\IncidenciasAlFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IncidenciaController extends AbstractController
{

    private $entityManager;
   

    public function __construct(EntityManagerInterface $entityManager)
    {
        
        $this->entityManager = $entityManager;
        
    }


    #[Route('/clientes/{clienteId}/incidencias/nueva', name: 'crear_incidencia')]
    public function crearIncidencia(Request $request, $clienteId): Response
    {
        // Obtener el cliente asociado al ID proporcionado
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $cliente = $this->entityManager->getRepository(Cliente::class)->find($clienteId);

        // Verificar si el cliente existe
        if (!$cliente) {
            throw $this->createNotFoundException('Cliente no encontrado');
        }

        // Crear una nueva instancia de la entidad Incidencia
        $incidencia = new Incidencia();
        $user = $this->getUser();

        $incidencia->setUser($user);
        // Crear un formulario para la incidencia
        $form = $this->createForm(IncidenciaFormType::class, $incidencia);

        // Manejar la solicitud del formulario
        $form->handleRequest($request);

        // Verificar si el formulario ha sido enviado y es válido
        if ($form->isSubmitted() && $form->isValid()) {
            // Asociar la incidencia al cliente
            $incidencia->setCliente($cliente);

            // Persistir la incidencia en la base de datos
            $entityManager = $this->entityManager;
            $entityManager->persist($incidencia);
            $entityManager->flush();
            $this->addFlash('ss', '¡Se ha insertado correctamente!');
            // Redirigir a la página de detalles del cliente
            return $this->redirectToRoute('ver_cliente', ['id' => $cliente->getId()]);
        }

        // Renderizar la plantilla con el formulario
        return $this->render('incidencia/crear_incidencia.html.twig', [
            'form' => $form->createView(),
            'cliente' => $cliente,
        ]);
    }

    #[Route('/clientes/{clienteId}/incidencias/editar/{id}', name: 'editar_incidencia')]
    public function editarIncidencia(Request $request, $clienteId, $id): Response
    {
        // Obtener el cliente asociado al ID proporcionado
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $cliente = $this->entityManager->getRepository(Cliente::class)->find($clienteId);
        $incidencia = $this->entityManager->getRepository(Incidencia::class)->find($id);
        // Verificar si el cliente existe
        if (!$incidencia) {
            throw $this->createNotFoundException('incidenica no encontrado');
        }
        if (!$cliente) {
            throw $this->createNotFoundException('Cliente no encontrado');
        }

        // Crear una nueva instancia de la entidad Incidencia

        $user = $this->getUser();

        $incidencia->setUser($user);
        // Crear un formulario para la incidencia
        $form = $this->createForm(IncidenciaFormType::class, $incidencia);

        // Manejar la solicitud del formulario
        $form->handleRequest($request);

        // Verificar si el formulario ha sido enviado y es válido
        if ($form->isSubmitted() && $form->isValid()) {
            // Asociar la incidencia al cliente
            $incidencia->setCliente($cliente);

            // Persistir la incidencia en la base de datos
            $entityManager = $this->entityManager;
            $entityManager->persist($incidencia);
            $entityManager->flush();
            $this->addFlash('ss', '¡Se ha editado correctamente!');
            // Redirigir a la página de detalles del cliente
            return $this->redirectToRoute('ver_cliente', ['id' => $cliente->getId()]);
        }

        // Renderizar la plantilla con el formulario
        return $this->render('incidencia/edit_incidencia.html.twig', [
            'form' => $form->createView(),
            'cliente' => $cliente,
            'incidencia'=>
            $incidencia,
        ]);
    }

    #[Route('/incidencia/{id}', name: 'ver_incidencia')]
    public function verIncidencia(Incidencia $incidencia): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->render('/incidencia/ver_incidencia.html.twig', [
            'incidencia' => $incidencia,
        ]);
    }

    #[Route('/incidencias', name: 'ver_incidencias')]
    public function verIncidencias(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $incidencias = $this->entityManager->getRepository(Incidencia::class)->findAll();
        return $this->render('/incidencia/index.html.twig', [
            'incidencias' => $incidencias,
        ]);
    }

    #[Route('/incidencias/{id}/borrar', name: 'borrar_incidencia')]
    public function borrarIncidencia(Incidencia $incidencia): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $this->entityManager->remove($incidencia);
        $this->entityManager->flush();
        $this->addFlash('ss', '¡Se ha borrado correctamente!');

        // Redirigir según tu lógica, posiblemente a la página de detalles del cliente
        return $this->redirectToRoute('ver_cliente', ['id' => $incidencia->getCliente()->getId()]);
    }

    #[Route('/incidencias/nueva', name: 'crear_al_incidencias')]
    public function crearAlIncidencias(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        // Fetch all clients to populate the dropdown
        $clientes = $this->entityManager->getRepository(Cliente::class)->findAll();

        // Create a new incidence
        $incidencia = new Incidencia();
        $user = $this->getUser();

        $incidencia->setUser($user);
        // Create a form with a dropdown to select the client
        $form = $this->createForm(IncidenciasAlFormType::class, $incidencia, [
            'clientes' => $clientes,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the incidence in the database
            $this->entityManager->persist($incidencia);
            $this->entityManager->flush();
            $this->addFlash('ss', '¡Se ha insertado correctamente!');

            // Redirect to the list of incidences
            return $this->redirectToRoute('ver_incidencias');
        }

        return $this->render('incidencia/crear_al_incidencias.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
