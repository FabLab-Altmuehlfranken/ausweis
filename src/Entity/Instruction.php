<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\InstructionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A workshop or instruction ("Einweisung"). Everybody who attended it
 * receives (or renews) all of its privileges.
 */
#[ORM\Entity(repositoryClass: InstructionRepository::class)]
#[ORM\UniqueConstraint(fields: ['name'])]
#[UniqueEntity(fields: ['name'])]
class Instruction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    /**
     * @var Collection<int, Privilege>
     */
    #[ORM\ManyToMany(targetEntity: Privilege::class)]
    #[Assert\Count(min: 1)]
    public private(set) Collection $privileges;

    public function __construct()
    {
        $this->privileges = new ArrayCollection();
    }

    public function addPrivilege(Privilege $privilege): void
    {
        if (!$this->privileges->contains($privilege)) {
            $this->privileges->add($privilege);
        }
    }

    public function removePrivilege(Privilege $privilege): void
    {
        $this->privileges->removeElement($privilege);
    }
}
