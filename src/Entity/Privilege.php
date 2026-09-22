<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PrivilegeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrivilegeRepository::class)]
#[ORM\UniqueConstraint(fields: ['name', 'area'])]
class Privilege
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(length: 255)]
    public private(set) string $name;

    #[ORM\ManyToOne(inversedBy: 'privileges')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) Area $area;

    #[ORM\Column(type: Types::TEXT)]
    public private(set) string $description;

    /**
     * @var Collection<int, Instruction>
     */
    #[ORM\ManyToMany(targetEntity: Instruction::class, mappedBy: 'privileges')]
    public private(set) Collection $instructions;

    /**
     * @var Collection<int, InstructionAssignment>
     */
    #[ORM\OneToMany(targetEntity: InstructionAssignment::class, mappedBy: 'privilege', orphanRemoval: true)]
    public private(set) Collection $instructionAssignments;

    public function __construct()
    {
        $this->instructions = new ArrayCollection();
        $this->instructionAssignments = new ArrayCollection();
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setArea(Area $area): static
    {
        $this->area = $area;

        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
