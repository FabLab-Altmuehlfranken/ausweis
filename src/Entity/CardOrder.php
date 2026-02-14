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
        #[ORM\JoinColumn(name: '`user`', nullable: false)]
        private(set) User $user,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }
}
