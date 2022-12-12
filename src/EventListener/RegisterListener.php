<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;


class RegisterListener 
{
    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        
        if(true == property_exists($entity,'firstname') && $entity instanceof User){

            $idEntity = $entity->getId();
            $firstname = $entity->getFirstname();
            $lastname = $entity->getLastname();
            $concatenation = ($lastname . '_' . $firstname . '_' . $idEntity);

            $entity->setPseudo($concatenation);
            $this->em->persist($entity);
            $this->em->flush();

        }
    }
}