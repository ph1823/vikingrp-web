<?php

namespace App\Events;

use App\Entity\Article;
use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private TokenInterface $tokenStorage;

    public function __construct(Security $security)
    {
        $this->tokenStorage = $security->getToken();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setAuthor'],
        ];
    }

    public function setAuthor(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Article)) {
            return;
        }

        $entity->setAuthor($this->tokenStorage->getUser());
    }
}