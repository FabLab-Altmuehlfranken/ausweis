<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\MachineCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/machine_categories', name: 'list_machine_categories')]
#[IsGranted(User::ADMIN_ROLE)]
final class ListMachineCategoriesController extends AbstractController
{
    public function __construct(
        private readonly MachineCategoryRepository $repository,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('list_machine_categories/index.html.twig', [
            'categories' => $this->repository->findBy([], ['name' => 'ASC']),
        ]);
    }
}
