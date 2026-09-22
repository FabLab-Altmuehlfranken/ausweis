<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MachineCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MachineCategoryRepository::class)]
#[ORM\UniqueConstraint(fields: ['name'])]
#[UniqueEntity(fields: ['name'])]
class MachineCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    /**
     * @var Collection<int, Privilege>
     */
    #[ORM\OneToMany(targetEntity: Privilege::class, mappedBy: 'category')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    public private(set) Collection $privileges;

    public function __construct(
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name = '',
    ) {
        $this->privileges = new ArrayCollection();
    }
}
