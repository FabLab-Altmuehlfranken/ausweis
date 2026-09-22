<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Instruction;
use App\Entity\InstructionAttendance;
use App\Entity\User;
use App\Form\BatchConfirmAttendanceType;
use App\Privilege\UserPrivilegeGranter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/instructions/{id}/confirm_attendances', name: 'batch_confirm_instruction_attendance')]
#[IsGranted(User::INSTRUCTOR_ROLE)]
final class BatchConfirmInstructionAttendanceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPrivilegeGranter $privilegeGranter,
    ) {
    }

    public function __invoke(
        Instruction $instruction,
        Request $request,
    ): Response {
        $form = $this->createForm(BatchConfirmAttendanceType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{users: iterable<User>, date: DateTimeImmutable} $data */
            $data = $form->getData();

            $this->handleAttendances($instruction, $data['users'], $data['date']);

            return $this->redirectToRoute('list_instructions');
        }

        return $this->render('batch_confirm_instruction_attendance/index.html.twig', [
            'instruction' => $instruction,
            'form' => $form,
        ]);
    }

    /**
     * @param iterable<User> $users
     */
    private function handleAttendances(Instruction $instruction, iterable $users, DateTimeImmutable $date): void
    {
        $instructor = $this->getUser();
        assert($instructor instanceof User);

        $confirmed = [];
        $skipped = [];
        foreach ($users as $user) {
            $attendance = $this->privilegeGranter->confirmAttendance($user, $instruction, $date, $instructor);
            if ($attendance instanceof InstructionAttendance) {
                $confirmed[] = htmlspecialchars($user->displayName);
            } else {
                $skipped[] = htmlspecialchars($user->displayName);
            }
        }

        $this->entityManager->flush();

        $instructionName = '<b>'.htmlspecialchars($instruction->name).'</b>';
        if ([] !== $confirmed) {
            $this->addFlash('success', 'Teilnahme an '.$instructionName.' bestätigt für: '.implode(', ', $confirmed));
        }

        if ([] !== $skipped) {
            $this->addFlash('info', 'Teilnahme an '.$instructionName.' an diesem Tag war bereits bestätigt für: '.implode(', ', $skipped));
        }
    }
}
