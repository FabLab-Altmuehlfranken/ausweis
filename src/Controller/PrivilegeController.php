<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\Privilege;
use App\Entity\User;
use App\Form\PrivilegeType;
use App\Repository\PrivilegeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/privileges')]
#[IsGranted(User::ADMIN_ROLE)]
final class PrivilegeController extends AbstractController
{
    #[Route(name: 'app_privilege_index', methods: ['GET'])]
    public function index(PrivilegeRepository $privilegeRepository): Response
    {
        return $this->render('privilege/index.html.twig', [
            'privileges' => $privilegeRepository->findBy(
                [],
                orderBy: ['area' => 'ASC', 'name' => 'ASC'],
            ),
        ]);
    }

    #[Route('/new', name: 'app_privilege_new', methods: ['GET', 'POST'])]
    #[Route('/new/{area}', name: 'app_privilege_new_in_area', methods: ['GET'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ?Area $area = null,
    ): Response {
        $privilege = new Privilege();
        if ($area) {
            $privilege->setArea($area);
        }
        $form = $this->createForm(PrivilegeType::class, $privilege);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($privilege);
            $entityManager->flush();

            $this->addFlash('success', 'Berechtigung erfolgreich angelegt.');

            return $this->redirectToRoute('app_privilege_show', ['id' => $privilege->id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('privilege/new.html.twig', [
            'privilege' => $privilege,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_privilege_show', methods: ['GET'])]
    public function show(Privilege $privilege): Response
    {
        return $this->render('privilege/show.html.twig', [
            'privilege' => $privilege,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_privilege_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Privilege $privilege, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrivilegeType::class, $privilege);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Berechtigung erfolgreich gespeichert.');

            return $this->redirectToRoute('app_privilege_show', ['id' => $privilege->id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('privilege/edit.html.twig', [
            'privilege' => $privilege,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_privilege_delete', methods: ['POST'])]
    public function delete(Request $request, Privilege $privilege, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$privilege->id, $request->getPayload()->getString('_token'))) {
            $entityManager->remove($privilege);
            $entityManager->flush();

            $this->addFlash('success', 'Berechtigung erfolgreich gelöscht.');
        }

        return $this->redirectToRoute('app_area_show', ['id' => $areaId], Response::HTTP_SEE_OTHER);
    }
}
