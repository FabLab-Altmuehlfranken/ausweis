<?php

declare(strict_types=1);

namespace App\Service;

use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UserDetailsQrCodeGenerator
{
    public function __construct(
        #[Target('user_details_svgQrCodeBuilder')]
        private BuilderInterface $qrCodeBuilder,
        private UrlGeneratorInterface $urlGenerator,
        #[Autowire(env: 'DEFAULT_URI')]
        private string $baseUrl,
    ) {
    }

    public function generate(Uuid $digitalCardId): ResultInterface
    {
        return $this->qrCodeBuilder->build(
            data: $this->buildUrl($digitalCardId),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            margin: 0,
            backgroundColor: new Color(0, 0, 0, 127),
        );
    }

    private function buildUrl(Uuid $digitalCardId): string
    {
        return $this->baseUrl.$this->urlGenerator->generate(
            'user_details_by_digital_card_id',
            ['uuid' => $digitalCardId->toString()],
        );
    }
}
