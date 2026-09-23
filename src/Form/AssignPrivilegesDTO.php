<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class AssignPrivilegesDTO
{
    /** @var Collection<int, User> */
    #[Assert\Count(min: 1)]
    public Collection $users;
}
