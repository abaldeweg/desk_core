<?php

namespace App\EventSubscriber;

use App\Entity\Letter;
use Baldeweg\Bundle\PdfBundle\Pdf;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LetterSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TokenStorageInterface $token, private readonly Pdf $pdf)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $letter = $args->getObject();

        if (!$letter instanceof Letter) {
            return;
        }

        $letter->setUser(
            $this->token->getToken()->getUser()
        );
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $letter = $args->getObject();

        if (!$letter instanceof Letter) {
            return;
        }

        $path = __DIR__.'/../../data/';

        if (!is_dir($path)) {
            mkdir($path);
        }

        $this->pdf->create(
            $path,
            str_replace('./', '', $letter->getTitle()),
            $letter->getContent(),
            $letter->getMeta()
        );
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postUpdate,
        ];
    }
}
