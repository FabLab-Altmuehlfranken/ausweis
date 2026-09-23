<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\InstructionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SortDirection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InstructionRepository::class)]
#[ORM\UniqueConstraint(fields: ['name'])]
class Instruction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    public private(set) string $name;

    /**
     * @var Collection<int, Privilege>
     */
    #[ORM\ManyToMany(targetEntity: Privilege::class, inversedBy: 'instructions')]
    #[ORM\OrderBy(['name' => SortDirection::Ascending])]
    #[Assert\Count(min: 1)]
    public private(set) Collection $privileges;

    public function __construct()
    {
        $this->privileges = new ArrayCollection();
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param Collection $privileges
     */
    public function setPrivileges(Collection $privileges): static
    {
        $this->privileges = $privileges;

        return $this;
    }
}
