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

final class DeleteCardOrderController extends AbstractController
{
    #[Route('/card_orders/{id}/delete', name: 'delete_card_order')]
    #[IsGranted(User::ADMIN_ROLE)]
    public function index(
        CardOrder $order,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(ConfirmType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleOrderDeletion($order, $entityManager);

            return $this->redirectToRoute('list_card_orders');
        }

        return $this->render('delete_card_order/index.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    private function handleOrderDeletion(
        CardOrder $order,
        EntityManagerInterface $entityManager,
    ): void {
        $userName = $order->user->displayName;

        $entityManager->remove($order);
        $entityManager->flush();

        $this->addFlash('info', 'Antrag von <b>'.$userName.'</b> erfolgreich gelöscht.');
    }
}
