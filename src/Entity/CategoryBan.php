<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryBanRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;

/**
 * Explicitly bans a user from all machines of a category, regardless of
 * granted privileges, until it is lifted.
 */
#[ORM\Entity(repositoryClass: CategoryBanRepository::class)]
class CategoryBan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    public private(set) readonly DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    public private(set) ?DateTimeImmutable $liftedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'lifted_by')]
    public private(set) ?User $liftedBy = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'categoryBans')]
        #[ORM\JoinColumn(name: '`user`', nullable: false)]
        public private(set) readonly User $user,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        public private(set) readonly MachineCategory $category,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(name: 'created_by', nullable: false)]
        public private(set) readonly User $createdBy,
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        public private(set) readonly ?string $reason,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function isActive(): bool
    {
        return !$this->liftedAt instanceof DateTimeImmutable;
    }

    public function lift(User $liftedBy): void
    {
        if (!$this->isActive()) {
            throw new LogicException('ban was lifted already');
        }

        $this->liftedAt = new DateTimeImmutable();
        $this->liftedBy = $liftedBy;
    }
}
