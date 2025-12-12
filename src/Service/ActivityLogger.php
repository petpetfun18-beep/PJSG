<?php
namespace App\Service;

use App\Entity\ActivityLogs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class ActivityLogger
{
    private EntityManagerInterface $em;
    private Security $security;
    private RequestStack $requestStack;

    public function __construct(EntityManagerInterface $em, Security $security, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function log(string $action, ?string $targetData = null): void
    {
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $log = new ActivityLogs(); // Match the entity name
        $log->setAction($action);
         $log->setTargetData($targetData ?? '');
        $log->setCreatedAt(new \DateTime()); // Fixed method name
        $log->setIpAddress($request ? $request->getClientIp() : null);

        if ($user) {
            $log->setUser($user);
            $log->setUsername($user->getUserIdentifier());
        }

        $this->em->persist($log);
        $this->em->flush();
    }
}
