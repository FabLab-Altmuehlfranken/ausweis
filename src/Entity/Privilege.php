<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PrivilegeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SortDirection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PrivilegeRepository::class)]
#[ORM\UniqueConstraint(fields: ['name', 'area'])]
class Privilege
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public private(set) string $name;

    #[ORM\ManyToOne(inversedBy: 'privileges')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    public private(set) Area $area;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public private(set) string $description;

    /**
     * @var Collection<int, Instruction>
     */
    #[ORM\ManyToMany(targetEntity: Instruction::class, mappedBy: 'privileges')]
    #[ORM\OrderBy(['name' => SortDirection::Ascending])]
    public private(set) Collection $instructions;

    /**
     * @var Collection<int, PrivilegeAssignment>
     */
    #[ORM\OneToMany(targetEntity: PrivilegeAssignment::class, mappedBy: 'privilege', orphanRemoval: true)]
    #[ORM\OrderBy(['user' => SortDirection::Ascending])]
    public private(set) Collection $privilegeAssignments;

    public function __construct()
    {
        $this->instructions = new ArrayCollection();
        $this->privilegeAssignments = new ArrayCollection();
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

    public function getDisplayName(): string
    {
        return $this->area->name.': '.$this->name;
    }
}
