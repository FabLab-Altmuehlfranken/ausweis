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

#[Route('/card_orders/{id}/delete', name: 'delete_card_order')]
#[IsGranted(User::ADMIN_ROLE)]
final class DeleteCardOrderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        CardOrder $order,
        Request $request,
    ): Response {
        $form = $this->createForm(ConfirmType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleOrderDeletion($order, $this->entityManager);

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
