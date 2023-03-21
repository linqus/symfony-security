<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Security\AccountNotVerifiedAuthenticationException;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{

    public function __construct(private RouterInterface $router) 
    {}

    public function onCheckPassport(CheckPassportEvent $event)
    {
        /**
         * @var Passport $passport
         */
        $passport = $event->getPassport();
        $user = $passport->getUser();
        if (!$user instanceof User) {
            throw new Exception('unexpected user type');
        }

        // if (!$user->getIsVerified()) {
        //     throw new CustomUserMessageAuthenticationException('Adres email niezweryfikowany');
        // }

        throw new AccountNotVerifiedAuthenticationException();
    }

    public function onLoginFailure(LoginFailureEvent $event)
    {
        if (!$event->getException() instanceof AccountNotVerifiedAuthenticationException) {
            return;
        }
        $response = new RedirectResponse($this->router->generate('app_verify_resend_email'));
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -100],
            LoginFailureEvent::class => ['onLoginFailure',0],
        ];
    }
}