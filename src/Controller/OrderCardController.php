<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderCardController extends AbstractController
{
    #[Route('/order_card', name: 'order_card')]
    public function index(): Response
    {
        if ($this->hasUserCardOrCardOrder()) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('order_card/index.html.twig');
    }

    private function hasUserCardOrCardOrder(): bool
    {
        $user = $this->getUser();
        assert($user instanceof User);

        if (is_int($user->cardId)) {
            return true;
        }

        return $user->hasOpenCardOrder();
    }
}
