<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\NotifiableComponent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface NotifiableComponentDAOManager extends DAOInterface
{
    /**
     * recherche du composentant notifiable
     * @param mixed $dataKey
     * @param string $entity
     * @return NotifiableComponent
     * @throws DAOException
     */
    public function findByNotifiable ($dataKey, string $entity): NotifiableComponent;
    
    /**
     * est-ce que ce composent notifiable existe??
     * @param int|string $dataKey
     * @param string $entity
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public function checkByNotifiable ($dataKey, string $entity) : bool;
    
    /**
     * chargement du notifiable dans le component
     * @param NotifiableComponent $notifiable
     */
    public function loadNotifiable (NotifiableComponent $notifiable) : void ;
}

