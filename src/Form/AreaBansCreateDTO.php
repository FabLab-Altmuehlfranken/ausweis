<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Area;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class AreaBansCreateDTO
{
    /** @var Collection<int, Area> */
    #[Assert\Count(min: 1)]
    public Collection $areas;
}
