<?php

namespace App\EventListener;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $user = $event->getUser();
        
        if (!$user instanceof Users) {
            return;
        }

        $user->setLastLoginAt(new \DateTimeImmutable());
        $this->em->persist($user);
        $this->em->flush();
    }
}