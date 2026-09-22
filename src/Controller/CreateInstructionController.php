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

#[Route('/instructions/new', name: 'create_instruction')]
#[IsGranted(User::ADMIN_ROLE)]
final class CreateInstructionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        Request $request,
    ): Response {
        $instruction = new Instruction();

        $form = $this->createForm(InstructionType::class, $instruction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($instruction);
            $this->entityManager->flush();

            $this->addFlash('success', 'Einweisung erfolgreich angelegt.');

            return $this->redirectToRoute('list_instructions');
        }

        return $this->render('edit_instruction/index.html.twig', [
            'form' => $form,
            'instruction' => null,
        ]);
    }
}
