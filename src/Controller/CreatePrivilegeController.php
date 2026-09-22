<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\MachineCategory;
use App\Entity\Privilege;
use App\Entity\User;
use App\Form\PrivilegeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/machine_categories/{id}/privileges/new', name: 'create_privilege')]
#[IsGranted(User::ADMIN_ROLE)]
final class CreatePrivilegeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        MachineCategory $category,
        Request $request,
    ): Response {
        $privilege = new Privilege($category);

        $form = $this->createForm(PrivilegeType::class, $privilege);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($privilege);
            $this->entityManager->flush();

            $this->addFlash('success', 'Berechtigung erfolgreich angelegt.');

            return $this->redirectToRoute('list_machine_categories');
        }

        return $this->render('edit_privilege/index.html.twig', [
            'form' => $form,
            'category' => $category,
            'privilege' => null,
        ]);
    }
}
