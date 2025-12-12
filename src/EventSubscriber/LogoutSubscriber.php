<?php

namespace App\EventSubscriber;

use App\Service\ActivityLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    private ActivityLogger $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($user) {
            $username = method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : 'Unknown';
            $id = method_exists($user, 'getId') ? $user->getId() : null;
            $roles = method_exists($user, 'getRoles') ? implode(', ', $user->getRoles()) : 'N/A';

            $action = sprintf(
                "User Login | ID: %s | Username: %s | Role: %s",
                $id ?? 'N/A',
                $username,
                $roles
            );

            $this->activityLogger->log($action);
        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()?->getUser();

        // Only log if a user object exists
        if ($user) {
            $username = method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : 'Unknown';
            $id = method_exists($user, 'getId') ? $user->getId() : null;

            $action = sprintf(
                "User Logout | ID: %s | Username: %s",
                $id ?? 'N/A',
                $username
            );

            $this->activityLogger->log($action);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
            LogoutEvent::class => 'onLogout',
        ];
    }
}
