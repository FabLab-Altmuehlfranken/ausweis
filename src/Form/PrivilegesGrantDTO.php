<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Privilege;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class PrivilegesGrantDTO
{
    /** @var Collection<int, Privilege> */
    #[Assert\Count(min: 1)]
    public Collection $privileges;
}
