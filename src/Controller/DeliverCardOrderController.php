<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardOrder;
use App\Entity\User;
use App\Form\ConfirmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeliverCardOrderController extends AbstractController
{
    #[Route('/card_orders/{id}/deliver', name: 'deliver_card_order')]
    #[IsGranted(User::ADMIN_ROLE)]
    public function index(
        CardOrder $order,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        if (!$order->isReadyForPickUp()) {
            $this->addFlash('error', 'Ausweis ist noch nicht bereit zur Abholung.');

            return $this->redirectToRoute('list_card_orders');
        }

        $form = $this->createForm(ConfirmType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleCardDelivery($order, $entityManager);

            return $this->redirectToRoute('list_card_orders');
        }

        return $this->render('deliver_card_order/index.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    private function handleCardDelivery(
        CardOrder $order,
        EntityManagerInterface $entityManager,
    ): void {
        $user = $order->user;

        $user->setCardId($order->cardId);
        $entityManager->persist($user);

        $entityManager->remove($order);

        $entityManager->flush();

        $this->addFlash('success', 'Ausweis wurde erfolgreich zugewiesen und kann jetzt an <b>'.$order->user->displayName.'</b> ausgehändigt werden.');
        $this->addFlash('info', 'Antrag von <b>'.$user->displayName.'</b> erfolgreich gelöscht.');
    }
}
