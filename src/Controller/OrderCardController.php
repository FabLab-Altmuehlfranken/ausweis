<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardOrder;
use App\Entity\User;
use App\Form\OrderCardType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderCardController extends AbstractController
{
    #[Route('/order_card', name: 'order_card')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->hasUserCardOrCardOrder()) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(OrderCardType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            assert($user instanceof User);
            $entityManager->persist(
                new CardOrder($user),
            );
            $entityManager->flush();

            // TODO send mail to let someone know about the new order
            $this->addFlash('success', 'Ausweis erfolgreich beantragt.');

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
}
