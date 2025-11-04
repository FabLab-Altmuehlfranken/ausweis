<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class OAuthConnector implements AccountConnectorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
