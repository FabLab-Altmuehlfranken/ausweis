<?php

declare(strict_types=1);

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    assert(is_string($context['APP_ENV']));

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
