<?php

namespace AppBundle\Events;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SoftDeleteListener
{
    // blameable field to update
    const DELETED_BY_FIELD = 'deletedBy';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function preSoftDelete(LifecycleEventArgs $event)
    {
        // get entity being soft deleted
        $entity = $event->getObject();

        // make sure the deleted by property exists
        $em = $event->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $reflProperties = $meta->getReflectionProperties();
        if ( ! array_key_exists(self::DELETED_BY_FIELD, $reflProperties)) {
            return;
        }

        // get old value
        $reflProperty = $meta->getReflectionProperty(self::DELETED_BY_FIELD);
        $oldValue = $reflProperty->getValue($entity);

        // get username
        $token = $this->tokenStorage->getToken();
        $username = 'anonymous';
        if ($token && is_object($token->getUser())) {
            $username = $token->getUser()->getUsername();
        }

        // schedule the update in the UnitOfWork
        $unitOfWork = $event->getObjectManager()->getUnitOfWork();
        $unitOfWork->propertyChanged($entity, self::DELETED_BY_FIELD, $oldValue, $username);
        $unitOfWork->scheduleExtraUpdate($entity, [
            self::DELETED_BY_FIELD => [$oldValue, $username]
        ]);
    }
}
