<?php

namespace App\EventSubscriber;

use App\Entity\Wiki;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class WikiSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $wiki = $args->getObject();

        if (!$wiki instanceof Wiki) {
            return;
        }

        $wiki->setUser(
            $this->token->getToken()->getUser()
        );
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }
}
