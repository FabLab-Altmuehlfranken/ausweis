<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\InstructionAttendanceRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Confirmation by an instructor that a user attended an Einweisung.
 */
#[ORM\Entity(repositoryClass: InstructionAttendanceRepository::class)]
class InstructionAttendance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    public private(set) readonly DateTimeImmutable $createdAt;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'attendances')]
        #[ORM\JoinColumn(name: '`user`', nullable: false)]
        public private(set) readonly User $user,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        public private(set) readonly Instruction $instruction,
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        public private(set) readonly DateTimeImmutable $date,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(name: 'confirmed_by', nullable: false)]
        public private(set) readonly User $confirmedBy,
    ) {
        $this->createdAt = new DateTimeImmutable();
        $user->addAttendance($this);
    }
}
