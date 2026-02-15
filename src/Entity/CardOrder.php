<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CardOrderRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CardOrderRepository::class)]
class CardOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    public private(set) DateTimeImmutable $createdAt;

    #[ORM\Column]
    public private(set) bool $isPrintOrdered = false;

    #[ORM\Column(nullable: true)]
    #[Assert\Regex('/^[0-9A-F]{2}(:[0-9A-F]{2}){6}$/')]
    #[Assert\NotBlank(allowNull: true)]
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
        $this->cardId = strtoupper($cardId);

        return $this;
    }
}
