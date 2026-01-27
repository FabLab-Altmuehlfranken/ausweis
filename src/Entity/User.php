<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Deprecated;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\OneToOne(mappedBy: 'requestedBy', cascade: ['persist', 'remove'])]
    private ?CardOrder $cardOrder = null;

    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $digitalCardId;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $cardId = null;

    public function __construct(
        #[Assert\Length(min: 3)]
        #[ORM\Column(length: 180)]
        private readonly string $username,
        #[ORM\Column(length: 255)]
        private string $displayName,
        #[ORM\Column(length: 255)]
        private string $mail,
    ) {
        $this->digitalCardId = Uuid::v4();
    }

    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        assert('' !== $this->username);

        return $this->username;
    }

    #[Override]
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param non-empty-string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $this->formatKeycloakRoles($roles);

        return $this;
    }

    #[Deprecated]
    #[Override]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    /**
     * @param string[] $roles
     *
     * @return list<non-empty-string>
     */
    protected function formatKeycloakRoles(array $roles): array
    {
        \Webmozart\Assert\Assert::allStringNotEmpty($roles);

        return array_values(
            array_map(
                static fn (string $role): string => 'ROLE_'.strtoupper($role),
                $roles,
            ),
        );
    }

    public function getCardOrder(): ?CardOrder
    {
        return $this->cardOrder;
    }

    public function setCardOrder(CardOrder $cardOrder): static
    {
        // set the owning side of the relation if necessary
        if ($cardOrder->getRequestedBy() !== $this) {
            $cardOrder->setRequestedBy($this);
        }

        $this->cardOrder = $cardOrder;

        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getDigitalCardId(): Uuid
    {
        return $this->digitalCardId;
    }

    public function setDigitalCardId(Uuid $digitalCardId): static
    {
        $this->digitalCardId = $digitalCardId;

        return $this;
    }

    public function getCardId(): ?string
    {
        return $this->cardId;
    }

    public function setCardId(?string $cardId): static
    {
        $this->cardId = $cardId;

        return $this;
    }

    public function getMail(): string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }
}
