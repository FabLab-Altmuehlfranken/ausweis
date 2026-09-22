<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\User;
use App\Form\AreaType;
use App\Repository\AreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/area')]
#[IsGranted(User::ADMIN_ROLE)]
final class AreaController extends AbstractController
{
    #[Route(name: 'app_area_index', methods: ['GET'])]
    public function index(AreaRepository $areaRepository): Response
    {
        return $this->render('area/index.html.twig', [
            'areas' => $areaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_area_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $area = new Area();
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($area);
            $entityManager->flush();

            $this->addFlash('success', 'Bereich erfolgreich angelegt.');

            return $this->redirectToRoute('app_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('area/new.html.twig', [
            'area' => $area,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_area_show', methods: ['GET'])]
    public function show(Area $area): Response
    {
        return $this->render('area/show.html.twig', [
            'area' => $area,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_area_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Area $area, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Bereich erfolgreich gespeichert.');

            return $this->redirectToRoute('app_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('area/edit.html.twig', [
            'area' => $area,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_area_delete', methods: ['POST'])]
    public function delete(Request $request, Area $area, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$area->id, $request->getPayload()->getString('_token'))) {
            $entityManager->remove($area);
            $entityManager->flush();

            $this->addFlash('success', 'Bereich erfolgreich gelöscht.');
        }

        return $this->redirectToRoute('app_area_index', [], Response::HTTP_SEE_OTHER);
    }
}
