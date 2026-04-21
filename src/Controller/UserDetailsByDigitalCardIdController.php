<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\UserDetailsQrCodeGenerator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserDetailsByDigitalCardIdController extends AbstractController
{
    #[Route('/user_details/{uuid}', name: 'user_details_by_digital_card_id')]
    public function index(
        UserDetailsQrCodeGenerator $qrCodeGenerator,
        #[MapEntity(mapping: ['uuid' => 'digitalCardId'])]
        User $user,
    ): Response {
        $qrCode = $qrCodeGenerator->generate($user->digitalCardId);

        return $this->render('user_details_by_digital_card_id/index.html.twig', [
            'user' => $user,
            'qrCode' => $qrCode,
        ]);
    }
}
