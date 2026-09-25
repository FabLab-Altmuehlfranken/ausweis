<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Privilege;
use App\Entity\PrivilegeAssignment;
use App\Entity\User;
use App\Form\PrivilegesGrantDTO;
use App\Form\PrivilegesGrantType;
use App\Form\PrivilegesRevokeType;
use App\Repository\PrivilegeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/privilege_assignments')]
#[IsGranted(User::ADMIN_ROLE)]
final class PrivilegeAssignmentController extends AbstractController
{
    #[Route('/{user}/grant', name: 'app_privilegeassignment_grant', methods: ['GET', 'POST'])]
    public function grant(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        PrivilegeRepository $privilegeRepository,
    ): Response {
        $allPrivileges = $privilegeRepository->findAll();
        $userPrivilegeIds = $user->getPrivileges()
            ->map(fn (Privilege $p): int => $p->id)
            ->toArray();

        $missingPrivileges = [];
        foreach ($allPrivileges as $privilege) {
            if (!in_array($privilege->id, $userPrivilegeIds, true)) {
                $missingPrivileges[$privilege->getDisplayName()] = $privilege;
            }
        }
        ksort($missingPrivileges, SORT_FLAG_CASE | SORT_NATURAL);

        if ([] === $missingPrivileges) {
            $this->addFlash('info', 'Der Benutzer hat schon alle Berechtigungen.');

            return $this->redirectToRoute('user_details_by_digital_card_id', ['uuid' => $user->digitalCardId], Response::HTTP_SEE_OTHER);
        }

        $dto = new PrivilegesGrantDTO();
        $form = $this->createForm(PrivilegesGrantType::class, $dto, ['choices' => $missingPrivileges]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->wrapInTransaction(
                function (EntityManagerInterface $entityManager) use ($dto, $user) {
                    foreach ($dto->privileges as $privilege) {
                        new PrivilegeAssignment()
                            ->setUser($user)
                            ->setPrivilege($privilege)
                            |> $entityManager->persist(...);
                    }
                },
            );

            $this->addFlash('success', 'Berechtigungen erfolgreich zugewiesen.');

            return $this->redirectToRoute('user_details_by_digital_card_id', ['uuid' => $user->digitalCardId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('privilege_assignment/grant.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{user}/revoke', name: 'app_privilegeassignment_revoke', methods: ['GET', 'POST'])]
    public function revoke(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrivilegesRevokeType::class, $user, ['choices' => $user->privilegeAssignments]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Berechtigungen erfolgreich entzogen.');

            return $this->redirectToRoute('user_details_by_digital_card_id', ['uuid' => $user->digitalCardId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('privilege_assignment/revoke.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
