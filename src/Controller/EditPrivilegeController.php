<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Privilege;
use App\Entity\User;
use App\Form\PrivilegeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/privileges/{id}/edit', name: 'edit_privilege')]
#[IsGranted(User::ADMIN_ROLE)]
final class EditPrivilegeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        Privilege $privilege,
        Request $request,
    ): Response {
        $form = $this->createForm(PrivilegeType::class, $privilege);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Berechtigung erfolgreich gespeichert.');

            return $this->redirectToRoute('list_machine_categories');
        }

        return $this->render('edit_privilege/index.html.twig', [
            'form' => $form,
            'category' => $privilege->category,
            'privilege' => $privilege,
        ]);
    }
}
