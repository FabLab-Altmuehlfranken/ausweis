<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Privilege;
use Doctrine\Common\Collections\Collection;

class PrivilegesGrantDTO
{
    /** @var Collection<int, Privilege> */
    public Collection $privileges;
}
