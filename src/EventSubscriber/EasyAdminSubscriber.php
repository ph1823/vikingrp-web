<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setWriteDate'],
        ];
    }

    public function setWriteDate(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Article)) {
            return;
        }

        if($entity->getWriteDate() == null) $entity->setWriteDate(new DateTime());
    }
}