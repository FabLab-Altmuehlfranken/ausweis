<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\InstructionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/instructions', name: 'list_instructions')]
#[IsGranted(User::INSTRUCTOR_ROLE)]
final class ListInstructionsController extends AbstractController
{
    public function __construct(
        private readonly InstructionRepository $repository,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('list_instructions/index.html.twig', [
            'instructions' => $this->repository->findBy([], ['name' => 'ASC']),
        ]);
    }
}
