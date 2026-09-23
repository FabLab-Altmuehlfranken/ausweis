<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PrivilegeAssignmentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrivilegeAssignmentRepository::class)]
class PrivilegeAssignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    public private(set) DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'privilegeAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) User $assignedTo;

    #[ORM\ManyToOne(inversedBy: 'privilegeAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) Privilege $privilege;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function setAssignedTo(User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    public function setPrivilege(Privilege $privilege): static
    {
        $this->privilege = $privilege;

        return $this;
    }
}
