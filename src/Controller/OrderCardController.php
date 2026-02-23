<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardOrder;
use App\Entity\User;
use App\Form\OrderCardType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class OrderCardController extends AbstractController
{
    #[Route('/order_card', name: 'order_card')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
    ): Response {
        if ($this->hasUserCardOrCardOrder()) {
            $this->addFlash('info', 'Du hast entweder bereits einen Ausweis oder schon einen beantragt.');

            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(OrderCardType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleCardOrder($entityManager, $mailer);

            return $this->redirectToRoute('homepage');
        }

        return $this->render('order_card/index.html.twig', [
            'form' => $form,
        ]);
    }

    private function hasUserCardOrCardOrder(): bool
    {
        $user = $this->getUser();
        assert($user instanceof User);

        if ($user->hasCard()) {
            return true;
        }

        return $user->hasOpenCardOrder();
    }

    private function handleCardOrder(
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
    ): void {
        $user = $this->getUser();
        assert($user instanceof User);

        $entityManager->persist(
            new CardOrder($user),
        );

        $mailer->send(
            new TemplatedEmail()
                ->to('vorstand@fablab-altmuehlfranken.de')
                ->subject('[FabLab] Neuer Ausweisantrag ('.$user->displayName.')')
                ->textTemplate('mail/new_card_order.txt.twig')
                ->context(['name' => $user->displayName])
        );

        /*
         * Flush after sending mail so in case there's an error we only sent a
         * mail to ourselves and did not create an order nobody will take care
         * about.
         */
        $entityManager->flush();

        $this->addFlash('success', 'Ausweis erfolgreich beantragt.');
    }
}
