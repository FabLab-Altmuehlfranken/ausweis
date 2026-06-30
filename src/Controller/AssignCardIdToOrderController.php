<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardOrder;
use App\Entity\User;
use App\Form\AssignCardIdToOrderType;
use App\Repository\CardOrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/card_orders/{id}/assign_card_id', name: 'assign_card_id_to_order')]
#[IsGranted(User::ADMIN_ROLE)]
final class AssignCardIdToOrderController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly CardOrderRepository $cardOrderRepository,
    ) {
    }

    public function __invoke(
        CardOrder $order,
        Request $request,
    ): Response {
        $form = $this->createForm(AssignCardIdToOrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->handleCardAssignment($this->entityManager, $order);
            } catch (NonUniqueCardIdException) {
                $this->addFlash('error', 'Ausweis-ID ist bereits einem anderen Benutzer zugewiesen');
            }

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
    ): void {
        $this->ensureUniqueCardId($order);

        $entityManager->flush();

        if ($order->cardId) {
            $this->addFlash('success', 'Ausweis-ID erfolgreich zugewiesen.');
        } else {
            $this->addFlash('info', 'Ausweis-ID erfolgreich entfernt.');
        }

        if ($order->isReadyForPickUp()) {
            $this->notifyUser($order);

            $this->addFlash('success', 'Ausweis bereit zur Abholung, der Benutzer wurde informiert.');
        }
    }

    public function notifyUser(CardOrder $order): void
    {
        $this->mailer->send(
            new TemplatedEmail()
                ->to($order->user->mail)
                ->subject('[FabLab] Ausweis bereit zu Abholung')
                ->textTemplate('mail/card_ready_for_pickup.txt.twig')
                ->context(['name' => $order->user->displayName])
        );
    }

    private function ensureUniqueCardId(CardOrder $order): void
    {
        if (!$order->cardId) {
            return;
        }

        $userWithSameCardId = $this->userRepository->findOneBy(['cardId' => $order->cardId]);
        if ($userWithSameCardId instanceof User) {
            throw new NonUniqueCardIdException();
        }

        $orderWithSameCardId = $this->cardOrderRepository->findOneBy(['cardId' => $order->cardId]);
        if (null === $orderWithSameCardId || $orderWithSameCardId === $order) {
            return;
        }

        throw new NonUniqueCardIdException();
    }
}
