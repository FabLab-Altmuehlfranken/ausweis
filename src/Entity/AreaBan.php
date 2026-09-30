<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AreaBanRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AreaBanRepository::class)]
#[ORM\UniqueConstraint(fields: ['area', 'user'])]
class AreaBan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    public private(set) DateTimeImmutable $createdAt;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) Area $area;

    #[ORM\ManyToOne(inversedBy: 'areaBans')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) User $user;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) User $bannedBy;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function setArea(Area $area): static
    {
        $this->area = $area;

        return $this;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function setBannedBy(User $bannedBy): static
    {
        $this->bannedBy = $bannedBy;

        return $this;
    }
}
