<?php

declare(strict_types=1);

namespace App\Privilege;

use App\Entity\MachineCategory;
use App\Entity\User;
use App\Entity\UserPrivilege;

final readonly class CategoryPrivileges
{
    /**
     * @param list<UserPrivilege> $privileges
     */
    public function __construct(
        public MachineCategory $category,
        public array $privileges,
    ) {
    }

    /**
     * Groups the privileges of a user by category.
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

        $result = [];
        foreach ($categories as $categoryId => $category) {
            $privileges = $privilegesByCategory[$categoryId] ?? [];
            usort($privileges, static fn (UserPrivilege $a, UserPrivilege $b): int => strcasecmp($a->privilege->name, $b->privilege->name));

            $result[] = new self($category, $privileges);
        }
        usort($result, static fn (self $a, self $b): int => strcasecmp($a->category->name, $b->category->name));

        return $result;
    }
}
