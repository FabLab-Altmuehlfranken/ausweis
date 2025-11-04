<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class OAuthConnector implements AccountConnectorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        if (!($user instanceof User)) {
            throw new InvalidArgumentException('User must be an instance of '.User::class);
        }

        $user->setUsername($response->getUserIdentifier());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
