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

#[Route('/machine_categories/new', name: 'create_machine_category')]
#[IsGranted(User::ADMIN_ROLE)]
final class CreateMachineCategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        Request $request,
    ): Response {
        $category = new MachineCategory();

        $form = $this->createForm(MachineCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'Gerätekategorie erfolgreich angelegt.');

            return $this->redirectToRoute('list_machine_categories');
        }

        return $this->render('edit_machine_category/index.html.twig', [
            'form' => $form,
            'category' => null,
        ]);
    }
}
