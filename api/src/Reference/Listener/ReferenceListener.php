<?php

/*
 * This file is part of the Sistim Informasi Antar Paroki (SIAP) project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SIAP\Reference\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use SIAP\Reference\Entity\Paroki;
use SIAP\Reference\Entity\RequireParokiInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReferenceListener implements EventSubscriber
{
    /**
     * @var TokenStorageInterface|null
     */
    private $tokenStorage;

    public function __construct(
        TokenStorageInterface $storage
    ) {
        $this->tokenStorage = $storage;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postPersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->doProcess($args, Events::prePersist);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->doProcess($args, Events::prePersist);
    }

    private function doProcess(LifecycleEventArgs $args, $event)
    {
        $entity = $args->getObject();

        if ($entity instanceof RequireParokiInterface && Events::prePersist === $event) {
            $this->setParoki($entity);
        }
    }

    private function setParoki(RequireParokiInterface $entity)
    {
        if ($entity->getParoki() instanceof Paroki) {
            return;
        }

        $currentUser = $this->getUser();

        if (null === $currentUser) {
            return;
        }
        $paroki = $currentUser->getParoki();
        if (null !== $paroki) {
            $entity->setParoki($paroki);
        }
    }

    private function getUser()
    {
        return null === $this->tokenStorage->getToken() ? null : $this->tokenStorage->getToken()->getUser();
    }
}
