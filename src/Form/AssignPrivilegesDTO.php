<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Area;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AssignPrivilegesDTO
{
    /** @var Collection<int, User> */
    #[Assert\Count(min: 1)]
    #[Assert\Valid]
    public Collection $users;

    /**
     * @param Collection<int, Area> $privilegeAreas
     */
    public function __construct(
        public readonly Collection $privilegeAreas,
    ) {
    }

    #[Assert\Callback]
    public function assertUsersAreNotBannedFromAreasOfNewPrivileges(
        ExecutionContextInterface $context,
        mixed $payload,
    ): void {
        foreach ($this->users as $user) {
            foreach ($this->privilegeAreas as $area) {
                if ($user->isBannedFromArea($area)) {
                    $context->buildViolation('Dem Benutzer "'.$user->displayName.'" wurde ein Verbot für den Bereich "'.$area->name.'" ausgesprochen, zuweisen von Berechtigungen nicht möglich.')
                        ->addViolation();
                }
            }
        }
    }
}
