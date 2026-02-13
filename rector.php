<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPhpSets()
    ->withImportNames()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        rectorPreset: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withAttributesSets()
    ->withComposerBased(
        twig: true,
        doctrine: true,
        symfony: true,
    );
