<?php

declare(strict_types=1);

namespace App\Privilege;

use App\Entity\CategoryBan;
use App\Entity\MachineCategory;
use App\Entity\User;
use App\Entity\UserPrivilege;

final readonly class CategoryPrivileges
{
    /**
     * @param list<UserPrivilege> $privileges including revoked ones
     */
    public function __construct(
        public MachineCategory $category,
        public ?CategoryBan $ban,
        public array $privileges,
    ) {
    }

    /**
     * @return list<UserPrivilege>
     */
    public function getActivePrivileges(): array
    {
        return array_values(array_filter(
            $this->privileges,
            static fn (UserPrivilege $privilege): bool => !$privilege->isRevoked(),
        ));
    }

    /**
     * Groups the privileges of a user by category, including categories the
     * user is banned from.
     *
     * @return list<self> sorted by category name
     */
    public static function fromUser(User $user): array
    {
        /** @var array<int, MachineCategory> $categories */
        $categories = [];
        /** @var array<int, list<UserPrivilege>> $privilegesByCategory */
        $privilegesByCategory = [];
        foreach ($user->privileges as $privilege) {
            $category = $privilege->privilege->category;
            $categories[$category->id] = $category;
            $privilegesByCategory[$category->id][] = $privilege;
        }

        foreach ($user->categoryBans as $ban) {
            if ($ban->isActive()) {
                $categories[$ban->category->id] = $ban->category;
            }
        }

        $result = [];
        foreach ($categories as $categoryId => $category) {
            $privileges = $privilegesByCategory[$categoryId] ?? [];
            usort($privileges, static fn (UserPrivilege $a, UserPrivilege $b): int => [$a->isRevoked(), strtolower($a->privilege->name)] <=> [$b->isRevoked(), strtolower($b->privilege->name)]);

            $result[] = new self($category, $user->getActiveBan($category), $privileges);
        }
        usort($result, static fn (self $a, self $b): int => strcasecmp($a->category->name, $b->category->name));

        return $result;
    }
}
