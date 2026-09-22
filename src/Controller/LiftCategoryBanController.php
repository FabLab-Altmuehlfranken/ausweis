<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CategoryBan;
use App\Entity\User;
use App\Form\ConfirmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/category_bans/{id}/lift', name: 'lift_category_ban')]
#[IsGranted(User::ADMIN_ROLE)]
final class LiftCategoryBanController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        CategoryBan $ban,
        Request $request,
    ): Response {
        $userDetailsRoute = ['uuid' => $ban->user->digitalCardId];

        if (!$ban->isActive()) {
            $this->addFlash('error', 'Sperre wurde bereits aufgehoben.');

            return $this->redirectToRoute('user_details_by_digital_card_id', $userDetailsRoute);
        }

        $form = $this->createForm(ConfirmType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $admin = $this->getUser();
            assert($admin instanceof User);

            $ban->lift($admin);
            $this->entityManager->flush();

            $this->addFlash('info', 'Sperre erfolgreich aufgehoben.');

            return $this->redirectToRoute('user_details_by_digital_card_id', $userDetailsRoute);
        }

        return $this->render('lift_category_ban/index.html.twig', [
            'ban' => $ban,
            'form' => $form,
        ]);
    }
}
