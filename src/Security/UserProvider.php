<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use RuntimeException;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

final readonly class UserProvider implements OAuthAwareUserProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        return $this->getUser($response);
    }

    private function getUser(UserResponseInterface $response): User
    {
        $username = $response->getUserIdentifier();
        $displayName = $response->getRealName()
            ?? throw new RuntimeException('got no real name from keycloak response');
        $mail = $response->getEmail()
            ?? throw new RuntimeException('got no mail from keycloak response');

        $user = $this->getUserFromRepository($username)
            ?? new User($username, $displayName, $mail);

        $user->setRoles($this->getRoles($response))
            ->setDisplayName($displayName)
            ->setMail($mail)
            ->updateLastLoginAt();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function getUserFromRepository(string $username): ?User
    {
        return $this->userRepository->findOneBy(
            ['username' => $username],
        );
    }

    /**
     * @return non-empty-string[]
     */
    private function getRoles(UserResponseInterface $response): array
    {
        if (!$response instanceof PathUserResponse) {
            throw new RuntimeException('unexpected user response type');
        }

        $data = $response->getData();
        if (
            !is_array($data['resource_access'] ?? null)
            || !is_array($data['resource_access']['ausweis'] ?? null)
            || !is_array($data['resource_access']['ausweis']['roles'] ?? null)
        ) {
            throw new RuntimeException('unexpected user response type');
        }
        $roles = $data['resource_access']['ausweis']['roles'];

        Assert::allStringNotEmpty($roles);

        return $roles;
    }
}
