<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;

/**
 * Ecosysteme d'une element notifiable
 * @author Esaie MUHASA
 *        
 */
class NotifiableComponent extends DBEntity
{
    /**
     * le nom simple de l'entite
     * @var string
     */
    private $entity;
    
    /**
     * la clee de reference vers le l'element notifiable
     * @var mixed
     */
    private $dataKey;
    
    /**
     * la reference vers l'element notifiable
     * @var Notifiable
     */
    private $notifiable;
    
    /**
     * @return string
     */
    public function getEntity() : string
    {
        return $this->entity;
    }

    /**
     * @return mixed
     */
    public function getDataKey()
    {
        return $this->dataKey;
    }

    /**
     * @return Notifiable
     */
    public function getNotifiable () : ?Notifiable
    {
        return $this->notifiable;
    }

    /**
     * @param string $entity
     */
    public function setEntity (string $entity) : void
    {
        $this->entity = $entity;
    }

    /**
     * @param mixed $dataKey
     */
    public function setDataKey ($dataKey) : void
    {
        $this->dataKey = $dataKey;
    }

    /**
     * @param Notifiable $notifiable
     */
    public function setNotifiable (Notifiable $notifiable) : void
    {
        $this->notifiable = $notifiable;
        $ref = new \ReflectionClass($notifiable);
        $this->setEntity($ref->getName());
        $this->setDataKey($notifiable->getKey());
    }

}

