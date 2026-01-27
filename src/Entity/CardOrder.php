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
    private DateTimeImmutable $createdAt;

    public function __construct(
        #[ORM\OneToOne(inversedBy: 'cardOrder', cascade: ['persist', 'remove'])]
        #[ORM\JoinColumn(nullable: false)]
        private User $requestedBy,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRequestedBy(): User
    {
        return $this->requestedBy;
    }

    public function setRequestedBy(User $requestedBy): static
    {
        $this->requestedBy = $requestedBy;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
