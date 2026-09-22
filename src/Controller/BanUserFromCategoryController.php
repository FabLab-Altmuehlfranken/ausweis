<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CategoryBan;
use App\Entity\MachineCategory;
use App\Entity\User;
use App\Form\BanCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users/{uuid}/ban', name: 'ban_user_from_category')]
#[IsGranted(User::ADMIN_ROLE)]
final class BanUserFromCategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'digitalCardId'])]
        User $user,
        Request $request,
    ): Response {
        $form = $this->createForm(BanCategoryType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{category: MachineCategory, reason: ?string} $data */
            $data = $form->getData();

            $this->handleBan($user, $data['category'], $data['reason']);

            return $this->redirectToRoute('user_details_by_digital_card_id', ['uuid' => $user->digitalCardId]);
        }

        return $this->render('ban_user_from_category/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    private function handleBan(User $user, MachineCategory $category, ?string $reason): void
    {
        if ($user->getActiveBan($category) instanceof CategoryBan) {
            $this->addFlash('error', 'Für diese Gerätekategorie besteht bereits eine Sperre.');

            return;
        }

        $admin = $this->getUser();
        assert($admin instanceof User);

        $this->entityManager->persist(
            new CategoryBan($user, $category, $admin, $reason),
        );
        $this->entityManager->flush();

        $this->addFlash('info', 'Sperre für <b>'.htmlspecialchars($category->name).'</b> erfolgreich eingetragen.');
    }
}
