<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\MachineCategory;
use App\Entity\User;
use App\Form\MachineCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/machine_categories/{id}/edit', name: 'edit_machine_category')]
#[IsGranted(User::ADMIN_ROLE)]
final class EditMachineCategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        MachineCategory $category,
        Request $request,
    ): Response {
        $form = $this->createForm(MachineCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Gerätekategorie erfolgreich gespeichert.');

            return $this->redirectToRoute('list_machine_categories');
        }

        return $this->render('edit_machine_category/index.html.twig', [
            'form' => $form,
            'category' => $category,
        ]);
    }
}
