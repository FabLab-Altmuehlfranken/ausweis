<?php

declare(strict_types=1);

namespace App\Entity;

use App\Privilege\PrivilegeStatus;
use App\Repository\UserPrivilegeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A privilege granted to a user. The first attended Einweisung is the initial
 * grant, all later ones are renewals.
 */
#[ORM\Entity(repositoryClass: UserPrivilegeRepository::class)]
class UserPrivilege
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    /**
     * @var Collection<int, InstructionAttendance>
     */
    #[ORM\ManyToMany(targetEntity: InstructionAttendance::class)]
    public private(set) Collection $attendances;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'privileges')]
        #[ORM\JoinColumn(name: '`user`', nullable: false)]
        public private(set) readonly User $user,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        public private(set) readonly Privilege $privilege,
    ) {
        $this->attendances = new ArrayCollection();
        $user->addPrivilege($this);
    }

    public function addAttendance(InstructionAttendance $attendance): void
    {
        if (!$this->attendances->contains($attendance)) {
            $this->attendances->add($attendance);
        }
    }

    /**
     * @return list<InstructionAttendance> oldest first
     */
    public function getSortedAttendances(): array
    {
        $attendances = array_values($this->attendances->toArray());
        usort($attendances, static fn (InstructionAttendance $a, InstructionAttendance $b): int => $a->date <=> $b->date);

        return $attendances;
    }

    public function getInitialAttendance(): ?InstructionAttendance
    {
        return $this->getSortedAttendances()[0] ?? null;
    }

    public function getLastRenewal(): ?InstructionAttendance
    {
        $attendances = $this->getSortedAttendances();
        if (count($attendances) < 2) {
            return null;
        }

        return $attendances[count($attendances) - 1];
    }

    public function getExpiryDate(): ?DateTimeImmutable
    {
        $attendances = $this->getSortedAttendances();
        $lastAttendance = end($attendances);
        if (!$lastAttendance instanceof InstructionAttendance) {
            return null;
        }

        return $this->privilege->getExpiryDate($lastAttendance->date);
    }

    public function getStatus(DateTimeImmutable $today = new DateTimeImmutable('today')): PrivilegeStatus
    {
        $expiryDate = $this->getExpiryDate();

        return match (true) {
            $expiryDate instanceof DateTimeImmutable && $expiryDate < $today => PrivilegeStatus::Expired,
            default => PrivilegeStatus::Valid,
        };
    }
}
