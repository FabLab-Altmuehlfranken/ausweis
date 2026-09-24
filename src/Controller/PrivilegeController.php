<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\Privilege;
use App\Entity\User;
use App\Form\PrivilegeType;
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
    #[Route('/new/{area}', name: 'app_privilege_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ?Area $area = null,
    ): Response {
        $privilege = new Privilege();
        if ($area instanceof Area) {
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
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_privilege_delete', methods: ['POST'])]
    public function delete(Request $request, Privilege $privilege, EntityManagerInterface $entityManager): RedirectResponse
    {
        $areaId = $privilege->area->id;
        if ($this->isCsrfTokenValid('delete_privilege'.$privilege->id, $request->getPayload()->getString('_token'))) {
            $name = $privilege->area->name.': '.$privilege->name;

            $entityManager->remove($privilege);
            $entityManager->flush();

            $this->addFlash('success', 'Berechtigung "'.$name.'" erfolgreich gelöscht.');
        }

        return $this->redirectToRoute('app_area_show', ['id' => $areaId], Response::HTTP_SEE_OTHER);
    }
}
