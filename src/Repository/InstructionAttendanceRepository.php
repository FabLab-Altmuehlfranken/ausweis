<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\InstructionAttendance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InstructionAttendance>
 */
final class InstructionAttendanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstructionAttendance::class);
    }
}
