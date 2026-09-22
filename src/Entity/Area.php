<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AreaRepository::class)]
#[ORM\UniqueConstraint(fields: ['name'])]
class Area
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
    #[ORM\OneToMany(targetEntity: Privilege::class, mappedBy: 'area', orphanRemoval: true)]
    #[Assert\NotBlank]
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
}
