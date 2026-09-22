<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserPrivilege;
use App\Form\ReasonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user_privileges/{id}/revoke', name: 'revoke_privilege')]
#[IsGranted(User::ADMIN_ROLE)]
final class RevokePrivilegeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        UserPrivilege $privilege,
        Request $request,
    ): Response {
        $userDetailsRoute = ['uuid' => $privilege->user->digitalCardId];

        if ($privilege->isRevoked()) {
            $this->addFlash('error', 'Berechtigung wurde bereits entzogen.');

            return $this->redirectToRoute('user_details_by_digital_card_id', $userDetailsRoute);
        }

        $form = $this->createForm(ReasonType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $admin = $this->getUser();
            assert($admin instanceof User);

            /** @var array{reason: ?string} $data */
            $data = $form->getData();

            $privilege->revoke($admin, $data['reason']);
            $this->entityManager->flush();

            $this->addFlash('info', 'Berechtigung erfolgreich entzogen.');

            return $this->redirectToRoute('user_details_by_digital_card_id', $userDetailsRoute);
        }

        return $this->render('revoke_privilege/index.html.twig', [
            'privilege' => $privilege,
            'form' => $form,
        ]);
    }
}
