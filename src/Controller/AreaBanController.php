<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\AreaBan;
use App\Entity\PrivilegeAssignment;
use App\Entity\User;
use App\Form\AreaBansCreateDTO;
use App\Form\AreaBansCreateType;
use App\Form\AreaBansRevokeType;
use App\Repository\AreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/area_bans')]
#[IsGranted(User::ROLE_INSTRUCTOR)]
final class AreaBanController extends AbstractController
{
    #[Route('/{user}/create', name: 'app_areaban_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        AreaRepository $areaRepository,
    ): Response {
        $instructor = $this->getUser();
        assert($instructor instanceof User);

        $allAreas = $areaRepository->findBy([], orderBy: ['name' => 'ASC']);
        $unbannedAreas = array_filter(
            $allAreas,
            static fn (Area $area): bool => !$user->isBannedFromArea($area),
        );

        if ([] === $unbannedAreas) {
            $this->addFlash('info', 'Der Benutzer hat schon ein Verbot für alle Bereiche.');

            return $this->redirectToRoute('user_details_by_digital_card_id', ['uuid' => $user->digitalCardId], Response::HTTP_SEE_OTHER);
        }

        $dto = new AreaBansCreateDTO();
        $form = $this->createForm(AreaBansCreateType::class, $dto, ['choices' => $unbannedAreas]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->wrapInTransaction(
                function (EntityManagerInterface $entityManager) use ($dto, $user, $instructor): void {
                    foreach ($dto->areas as $area) {
                        $user->getPrivilegeAssignmentsInArea($area)
                            ->map(
                                fn (PrivilegeAssignment $privilegeAssignment) => $entityManager->remove($privilegeAssignment),
                            );
                        new AreaBan()
                            ->setUser($user)
                            ->setArea($area)
                            ->setBannedBy($instructor)
                            |> $entityManager->persist(...);
                    }
                },
            );

            $this->addFlash('success', 'Verbote erfolgreich angelegt, vorhandene Berechtigungen wurden entzogen.');

            return $this->redirectToRoute('user_details_by_digital_card_id', ['uuid' => $user->digitalCardId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('privilege_assignment/grant.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{user}/revoke', name: 'app_areaban_revoke', methods: ['GET', 'POST'])]
    public function revoke(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $areaBans = [];
        foreach ($user->getAreaBans()->toArray() as $areaBan) {
            $areaBans[$areaBan->area->name] = $areaBan;
        }
        ksort($areaBans, SORT_FLAG_CASE | SORT_NATURAL);

        $form = $this->createForm(AreaBansRevokeType::class, $user, ['choices' => $areaBans]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Verbote erfolgreich aufgehoben.');

            return $this->redirectToRoute('user_details_by_digital_card_id', ['uuid' => $user->digitalCardId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('privilege_assignment/revoke.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
