<?php
namespace PHPBackend\Dao;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class DAOListenerItem
{
    /**
     * liste des evenements ecoutables
     * @var int
     */
    const LISTENABLE_EVENTS = [DAOEvent::TYPE_CREATION, DAOEvent::TYPE_UPDATION, DAOEvent::TYPE_DELETION];
    
    /**
     * @var DAOListener
     */
    private $listener;
    
    /**
     * le type d'evement ecouter
     * @var int[]
     */
    private $types = [];

    /**
     * constructeur d'initialisation
     * @param DAOListener $listener
     * @param int|int[] $types
     */
    public function __construct(DAOListener $listener, $types = null)
    {
        $this->listener = $listener;
        $this->setTypes($types);
    }
    
    /**
     * @return \PHPBackend\Dao\DAOListener
     */
    public function getListener() : DAOListener
    {
        return $this->listener;
    }

    /**
     * @return int[]
     */
    public function getTypes() : ?array
    {
        return $this->type;
    }
    
    /**
     * est-ce que ce type d'evenement concerne cette ecouteur???
     * @param int $type
     * @return bool
     */
    public function isListen (int $type) : bool {
        
        if ($this->types == null || empty($this->types)) {//pour ceux qui ecoute tout les evenements
            return true;
        }
        
        foreach ($this->types as $t) {
            if ($t == $type) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * mutateur des types d'evenements ecouter
     * @param int|int[] $types
     */
    private function setTypes ($types) : void {
        if($types == null) {
            $this->types = self::LISTENABLE_EVENTS;
        } elseif (is_array($types)) {
            $this->types = $types;
        } elseif (is_int($types)) {
            $this->types[] = $types;
        }
    }

}

