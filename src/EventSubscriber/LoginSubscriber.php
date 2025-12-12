<?php

namespace App\EventSubscriber;

use App\Service\ActivityLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(private ActivityLogger $activityLogger) {}

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user) {
            return;
        }

        $action = sprintf(
            "User Login | ID: %s | Username: %s | Role: %s",
            method_exists($user, 'getId') ? $user->getId() : 'N/A',
            method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : 'Unknown',
            method_exists($user, 'getRoles') ? implode(', ', $user->getRoles()) : 'N/A'
        );

        $this->activityLogger->log($action);
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()?->getUser();

        if (!$user) {
            return;
        }

        $action = sprintf(
            "User Logout | ID: %s | Username: %s",
            method_exists($user, 'getId') ? $user->getId() : 'N/A',
            method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : 'Unknown'
        );

        $this->activityLogger->log($action);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onInteractiveLogin',
            LogoutEvent::class => 'onLogout',
        ];
    }
}
