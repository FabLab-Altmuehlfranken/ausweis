<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Instruction;
use App\Entity\PrivilegeAssignment;
use App\Entity\User;
use App\Form\AssignPrivilegesDTO;
use App\Form\AssignPrivilegesType;
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

            $this->addFlash('success', 'Einweisung erfolgreich angelegt.');

            return $this->redirectToRoute('app_instruction_show', ['id' => $instruction->id], Response::HTTP_SEE_OTHER);
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

            $this->addFlash('success', 'Einweisung erfolgreich gespeichert.');

            return $this->redirectToRoute('app_instruction_show', ['id' => $instruction->id], Response::HTTP_SEE_OTHER);
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
            $name = $instruction->name;

            $entityManager->remove($instruction);
            $entityManager->flush();

            $this->addFlash('success', 'Einweisung "'.$name.'" erfolgreich gelöscht.');
        }

        return $this->redirectToRoute('app_instruction_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/assign_prigileves', name: 'app_instruction_assign_privileges', methods: ['GET', 'POST'])]
    public function assignPrivileges(Request $request, Instruction $instruction, EntityManagerInterface $entityManager): Response
    {
        $dto = new AssignPrivilegesDTO();
        $form = $this->createForm(AssignPrivilegesType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($instruction->privileges as $privilege) {
                foreach ($dto->users as $user) {
                    $privilegeAssignment = $user->privilegeAssignments->findFirst(
                        fn (int $k, PrivilegeAssignment $v): bool => $v->privilege->id === $privilege->id,
                    ) ?? new PrivilegeAssignment()->setUser($user)->setPrivilege($privilege);
                    $privilegeAssignment->renewAssignment();
                    $entityManager->persist($privilegeAssignment);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Berechtigungen erfolgreich vergeben.');

            return $this->redirectToRoute('app_instruction_show', ['id' => $instruction->id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instruction/assign_prigileges.html.twig', [
            'instruction' => $instruction,
            'form' => $form,
        ]);
    }
}
