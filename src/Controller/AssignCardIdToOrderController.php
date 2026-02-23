<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardOrder;
use App\Entity\User;
use App\Form\AssignCardIdToOrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AssignCardIdToOrderController extends AbstractController
{
    #[Route('/card_orders/{id}/assign_card_id', name: 'assign_card_id_to_order')]
    #[IsGranted(User::ADMIN_ROLE)]
    public function index(
        CardOrder $order,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
    ): Response {
        $form = $this->createForm(AssignCardIdToOrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleCardAssignment($entityManager, $order, $mailer);

            return $this->redirectToRoute('list_card_orders');
        }

        return $this->render('assign_card_id_to_order/index.html.twig', [
            'form' => $form,
            'order' => $order,
        ]);
    }

    private function handleCardAssignment(
        EntityManagerInterface $entityManager,
        CardOrder $order,
        MailerInterface $mailer,
    ): void {
        $entityManager->flush();

        if ($order->cardId) {
            $this->addFlash('success', 'Ausweis-ID erfolgreich zugewiesen.');
        } else {
            $this->addFlash('info', 'Ausweis-ID erfolgreich entfernt.');
        }

        if ($order->isReadyForPickUp()) {
            $mailer->send(
                new TemplatedEmail()
                    ->to($order->user->mail)
                    ->subject('[FabLab] Ausweis bereit zu Abholung')
                    ->textTemplate('mail/card_ready_for_pickup.txt.twig')
                    ->context(['name' => $order->user->displayName])
            );

            $this->addFlash('success', 'Ausweis bereit zur Abholung, der Benutzer wurde informiert.');
        }
    }
}
