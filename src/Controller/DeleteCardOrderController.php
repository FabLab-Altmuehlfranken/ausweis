<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardOrder;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteCardOrderController extends AbstractController
{
    #[Route('/card_orders/{id}/delete', name: 'delete_card_order')]
    #[IsGranted(User::ADMIN_ROLE)]
    public function index(
        CardOrder $order,
        EntityManagerInterface $entityManager,
    ): RedirectResponse {
        $userName = $order->user->displayName;
        $entityManager->remove($order);
        $entityManager->flush();
        $this->addFlash('info', 'Antrag von <b>'.$userName.'</b> erfolgreich gelöscht.');

        return $this->redirectToRoute('list_card_orders');
    }
}
