<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BeforeRequestListener
{
    /** @var EntityManager */
    private $em;
    /** @var User|null */
    private $user;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, Security $security)
    {
        $this->em = $em;
        $this->user = $security->getUser();
    }

    public function onKernelController(ControllerEvent $event)
    {
        $filter = $this->em->getFilters()->enable('userfilter');
        $filter->setParameter('userId', ($this->user ? $this->user->getId()->toString() : null));
    }
}