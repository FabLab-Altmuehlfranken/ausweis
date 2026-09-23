<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Instruction;
use App\Entity\User;
use App\Form\InstructionType;
use App\Repository\InstructionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/instructions')]
#[IsGranted(User::ADMIN_ROLE)]
final class InstructionController extends AbstractController
{
    #[Route(name: 'app_instruction_index', methods: ['GET'])]
    public function index(InstructionRepository $instructionRepository): Response
    {
        return $this->render('instruction/index.html.twig', [
            'instructions' => $instructionRepository->findBy([], orderBy: ['name' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_instruction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $instruction = new Instruction();
        $form = $this->createForm(InstructionType::class, $instruction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($instruction);
            $entityManager->flush();

            $this->addFlash('success', 'Berechtigung erfolgreich angelegt.');

            return $this->redirectToRoute('app_instruction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instruction/new.html.twig', [
            'instruction' => $instruction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instruction_show', methods: ['GET'])]
    public function show(Instruction $instruction): Response
    {
        return $this->render('instruction/show.html.twig', [
            'instruction' => $instruction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_instruction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Instruction $instruction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InstructionType::class, $instruction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Bereich erfolgreich gespeichert.');

            return $this->redirectToRoute('app_instruction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instruction/edit.html.twig', [
            'instruction' => $instruction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instruction_delete', methods: ['POST'])]
    public function delete(Request $request, Instruction $instruction, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$instruction->id, $request->getPayload()->getString('_token'))) {
            $entityManager->remove($instruction);
            $entityManager->flush();

            $this->addFlash('success', 'Bereich erfolgreich gelöscht.');
        }

        return $this->redirectToRoute('app_instruction_index', [], Response::HTTP_SEE_OTHER);
    }
}
