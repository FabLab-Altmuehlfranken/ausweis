<?php

declare(strict_types=1);

namespace App\Privilege;

use App\Entity\Instruction;
use App\Entity\InstructionAttendance;
use App\Entity\User;
use App\Entity\UserPrivilege;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Confirms Einweisung attendances and grants (or renews) all privileges of
 * the attended Einweisung.
 */
final readonly class UserPrivilegeGranter
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Does not flush the entity manager.
     *
     * @return InstructionAttendance|null null if the attendance was confirmed already
     */
    public function confirmAttendance(
        User $user,
        Instruction $instruction,
        DateTimeImmutable $date,
        User $confirmedBy,
    ): ?InstructionAttendance {
        if ($user->hasAttended($instruction, $date)) {
            return null;
        }

        $attendance = new InstructionAttendance($user, $instruction, $date, $confirmedBy);
        $this->entityManager->persist($attendance);
        $this->grant($attendance);

        return $attendance;
    }

    private function grant(InstructionAttendance $attendance): void
    {
        $user = $attendance->user;

        foreach ($attendance->instruction->privileges as $privilege) {
            $userPrivilege = $user->getActivePrivilege($privilege);
            if (!$userPrivilege instanceof UserPrivilege) {
                $userPrivilege = new UserPrivilege($user, $privilege);
                $this->entityManager->persist($userPrivilege);
            }

            $userPrivilege->addAttendance($attendance);
        }
    }
}
