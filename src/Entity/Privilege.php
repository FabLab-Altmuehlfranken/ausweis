<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PrivilegeRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PrivilegeRepository::class)]
#[ORM\UniqueConstraint(columns: ['category_id', 'name'])]
#[UniqueEntity(fields: ['category', 'name'])]
class Privilege
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    /**
     * Number of months a granted privilege stays valid after the last
     * Einweisung, null if it never expires.
     */
    #[ORM\Column(nullable: true)]
    #[Assert\Positive]
    public ?int $validityMonths = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'privileges')]
        #[ORM\JoinColumn(nullable: false)]
        public private(set) readonly MachineCategory $category,
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name = '',
    ) {
    }

    public function getExpiryDate(DateTimeImmutable $grantedAt): ?DateTimeImmutable
    {
        if (null === $this->validityMonths) {
            return null;
        }

        /*
         * Avoid PHP's month overflow (e.g. Jan 31st + 1 month = Mar 3rd) by
         * clamping to the last day of the target month.
         */
        $firstOfTargetMonth = $grantedAt->modify('first day of this month')
            ->modify('+'.$this->validityMonths.' months');
        $day = min(
            (int) $grantedAt->format('j'),
            (int) $firstOfTargetMonth->format('t'),
        );

        return $firstOfTargetMonth->setDate(
            (int) $firstOfTargetMonth->format('Y'),
            (int) $firstOfTargetMonth->format('n'),
            $day,
        );
    }
}
