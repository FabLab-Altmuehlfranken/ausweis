<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\UserDetailsQrCodeGenerator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user_details/{uuid}', name: 'user_details_by_digital_card_id')]
final class UserDetailsByDigitalCardIdController extends AbstractController
{
    public function __construct(
        private readonly UserDetailsQrCodeGenerator $qrCodeGenerator,
    ) {
    }

    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'digitalCardId'])]
        User $user,
    ): Response {
        $qrCode = $this->qrCodeGenerator->generate($user->digitalCardId);

        return $this->render('user_details_by_digital_card_id/index.html.twig', [
            'user' => $user,
            'qrCode' => $qrCode,
        ]);
    }
}
