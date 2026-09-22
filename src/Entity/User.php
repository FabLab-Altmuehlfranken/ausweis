<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\UniqueConstraint(fields: ['digitalCardId'])]
#[ORM\UniqueConstraint(fields: ['cardId'])]
class User implements UserInterface
{
    public const string MEMBER_ROLE = 'ROLE_MEMBER';
    public const string ADMIN_ROLE = 'ROLE_ADMIN';
    public const string INSTRUCTOR_ROLE = 'ROLE_INSTRUCTOR';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    public private(set) ?CardOrder $cardOrder = null;

    #[ORM\Column(type: UuidType::NAME)]
    public private(set) Uuid $digitalCardId;

    #[ORM\Column(nullable: true)]
    public private(set) ?string $cardId = null;

    /**
     * @var Collection<int, UserPrivilege>
     */
    #[ORM\OneToMany(targetEntity: UserPrivilege::class, mappedBy: 'user')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    public private(set) Collection $privileges;

    /**
     * @var Collection<int, InstructionAttendance>
     */
    #[ORM\OneToMany(targetEntity: InstructionAttendance::class, mappedBy: 'user')]
    #[ORM\OrderBy(['date' => 'DESC'])]
    public private(set) Collection $attendances;

    public function __construct(
        #[Assert\Length(min: 3)]
        #[ORM\Column(length: 180)]
        private readonly string $username,
        #[ORM\Column(length: 255)]
        public private(set) string $displayName,
        #[ORM\Column(length: 255)]
        public private(set) string $mail,
    ) {
        $this->digitalCardId = Uuid::v4();
        $this->privileges = new ArrayCollection();
        $this->attendances = new ArrayCollection();
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

    public function setCardId(?string $cardId): static
    {
        $this->cardId = $cardId;

        return $this;
    }

    public function hasCard(): bool
    {
        return is_string($this->cardId);
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function hasOpenCardOrder(): bool
    {
        return $this->cardOrder instanceof CardOrder;
    }

    public function isMember(): bool
    {
        return $this->hasRole(self::MEMBER_ROLE);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ADMIN_ROLE);
    }

    /**
     * @internal use UserPrivilege::__construct()
     */
    public function addPrivilege(UserPrivilege $privilege): void
    {
        if (!$this->privileges->contains($privilege)) {
            $this->privileges->add($privilege);
        }
    }

    /**
     * @internal use InstructionAttendance::__construct()
     */
    public function addAttendance(InstructionAttendance $attendance): void
    {
        if (!$this->attendances->contains($attendance)) {
            $this->attendances->add($attendance);
        }
    }

    public function hasAttended(Instruction $instruction, DateTimeImmutable $date): bool
    {
        foreach ($this->attendances as $attendance) {
            if ($attendance->instruction === $instruction
                && $attendance->date->format('Y-m-d') === $date->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }

    public function getActivePrivilege(Privilege $privilege): ?UserPrivilege
    {
        foreach ($this->privileges as $userPrivilege) {
            if ($userPrivilege->privilege === $privilege) {
                return $userPrivilege;
            }
        }

        return null;
    }

    private function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }
}
