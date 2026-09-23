<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PrivilegeAssignmentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrivilegeAssignmentRepository::class)]
#[ORM\UniqueConstraint(fields: ['user', 'privilege'])]
class PrivilegeAssignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    public private(set) DateTimeImmutable $timestamp;

    #[ORM\ManyToOne(inversedBy: 'privilegeAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) User $user;

    #[ORM\ManyToOne(inversedBy: 'privilegeAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) Privilege $privilege;

    public function __construct()
    {
        $this->timestamp = new DateTimeImmutable();
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function setPrivilege(Privilege $privilege): static
    {
        $this->privilege = $privilege;

        return $this;
    }

    public function renewAssignment(): void
    {
        $this->timestamp = new DateTimeImmutable();
    }
}
