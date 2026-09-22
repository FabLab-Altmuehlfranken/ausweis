<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Instruction;
use App\Entity\User;
use App\Form\InstructionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/instructions/{id}/edit', name: 'edit_instruction')]
#[IsGranted(User::ADMIN_ROLE)]
final class EditInstructionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        Instruction $instruction,
        Request $request,
    ): Response {
        $form = $this->createForm(InstructionType::class, $instruction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Einweisung erfolgreich gespeichert.');

            return $this->redirectToRoute('list_instructions');
        }

        return $this->render('edit_instruction/index.html.twig', [
            'form' => $form,
            'instruction' => $instruction,
        ]);
    }
}
