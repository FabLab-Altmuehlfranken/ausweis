<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserPrivilege;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPrivilege>
 */
final class UserPrivilegeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPrivilege::class);
    }
}
