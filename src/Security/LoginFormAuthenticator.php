<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'security_login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return $request->request->get('login'); //arrays avec 3 info
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            return $userProvider->loadUserByUsername($credentials['email']);
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException("Cette aresse email n'est pas connue !");
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // verifier que le mot de passe fourni, corresponde avec le mot de passe de la base de donnée
        // $cedentials['password']=> $user->getPassword()
        $isValid = $this->encoder->isPasswordValid($user, $credentials['password']);

        if (!$isValid) {
            throw new AuthenticationException("Les informations ne correspondent pas !");
        }
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // todo
        return new RedirectResponse('/');
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
