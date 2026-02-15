<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CardOrderRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardOrderRepository::class)]
class CardOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    public private(set) DateTimeImmutable $createdAt;

    #[ORM\Column]
    public private(set) bool $isPrintOrdered = false;

    #[ORM\Column(nullable: true)]
    public private(set) ?string $cardId = null;

    public function __construct(
        #[ORM\OneToOne(inversedBy: 'cardOrder', cascade: ['persist', 'remove'])]
        #[ORM\JoinColumn(name: '`user`', nullable: false)]
        private(set) User $user,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function setCardId(string $cardId): static
    {
        $this->cardId = $cardId;

        return $this;
    }
}
