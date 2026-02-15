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

final class DeliverCardOrderController extends AbstractController
{
    #[Route('/card_orders/{id}/deliver', name: 'deliver_card_order')]
    #[IsGranted(User::ADMIN_ROLE)]
    public function index(
        CardOrder $order,
        EntityManagerInterface $entityManager,
    ): RedirectResponse {
        if (!$order->isReadyForPickUp()) {
            $this->addFlash('error', 'Ausweis ist noch nicht bereit zur Abholung.');

            return $this->redirectToRoute('list_card_orders');
        }

        $user = $order->user;
        $user->setCardId($order->cardId);
        $entityManager->persist($user);
        $entityManager->remove($order);
        $entityManager->flush();
        $this->addFlash('success', 'Ausweis wurde erfolgreich zugewiesen und kann jetzt an <b>'.$order->user->displayName.'</b> ausgehändigt werden.');
        $this->addFlash('info', 'Antrag von <b>'.$user->displayName.'</b> erfolgreich gelöscht.');

        return $this->redirectToRoute('list_card_orders');
    }
}
