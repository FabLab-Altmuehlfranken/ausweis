<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Instruction;
use App\Entity\InstructionAttendance;
use App\Entity\User;
use App\Form\ConfirmAttendanceType;
use App\Privilege\UserPrivilegeGranter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users/{uuid}/confirm_attendance', name: 'confirm_instruction_attendance')]
#[IsGranted(User::INSTRUCTOR_ROLE)]
final class ConfirmInstructionAttendanceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPrivilegeGranter $privilegeGranter,
    ) {
    }

    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'digitalCardId'])]
        User $user,
        Request $request,
    ): Response {
        $form = $this->createForm(ConfirmAttendanceType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{instruction: Instruction, date: DateTimeImmutable} $data */
            $data = $form->getData();

            $this->handleAttendance($user, $data['instruction'], $data['date']);

            return $this->redirectToRoute('user_details_by_digital_card_id', ['uuid' => $user->digitalCardId]);
        }

        return $this->render('confirm_instruction_attendance/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    private function handleAttendance(User $user, Instruction $instruction, DateTimeImmutable $date): void
    {
        $instructor = $this->getUser();
        assert($instructor instanceof User);

        $attendance = $this->privilegeGranter->confirmAttendance($user, $instruction, $date, $instructor);
        if (!$attendance instanceof InstructionAttendance) {
            $this->addFlash('info', 'Teilnahme an <b>'.htmlspecialchars($instruction->name).'</b> an diesem Tag wurde bereits bestätigt.');

            return;
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Teilnahme an <b>'.htmlspecialchars($instruction->name).'</b> erfolgreich bestätigt.');
    }
}
