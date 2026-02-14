<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\CardOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ListCardOrdersController extends AbstractController
{
    public function __construct(
        private readonly CardOrderRepository $repository,
    ) {
    }

    #[Route('/card_orders', name: 'list_card_orders')]
    #[IsGranted(User::ADMIN_ROLE)]
    public function index(): Response
    {
        $orders = $this->repository->findAll();

        return $this->render('list_card_orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
