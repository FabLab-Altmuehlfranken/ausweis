<?php

namespace App\Security;

use App\Entity\User;
use HWI\Bundle\OAuthBundle\Form\RegistrationFormHandlerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class FormHandler implements RegistrationFormHandlerInterface
{
    public function process(Request $request, FormInterface $form, UserResponseInterface $userInformation): bool
    {
        $user = new User();
        $form->setData($user);
        $form->handleRequest($request);

        return $form->isSubmitted() && $form->isValid();
    }
}
