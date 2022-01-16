<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\NotifiableComponent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
abstract class NotifiableComponentDAOManager extends DefaultDAOInterface
{
    /**
     * recherche du composentant notifiable
     * @param mixed $dataKey
     * @param string $entity
     * @return NotifiableComponent
     * @throws DAOException
     */
    public abstract function findByNotifiable ($dataKey, string $entity): NotifiableComponent;
    
    /**
     * est-ce que ce composent notifiable existe??
     * @param int|string $dataKey
     * @param string $entity
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public function checkByNotifiable ($dataKey, string $entity) : bool {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['dataKey' => $dataKey, 'entity' => $entity]);
    }
    
    /**
     * chargement du notifiable dans le component
     * @param NotifiableComponent $notifiable
     */
    public function loadNotifiable (NotifiableComponent $notifiable) : void {
        if ($this->getDaoManager()->getManagerOf($notifiable->getEntity())->idExist($notifiable->getDataKey())) {
            $notifiable->setNotifiable($this->getDaoManager()->getManagerOf($notifiable->getEntity())->getForId($notifiable->getDataKey()));
        } else {
            throw new DAOException("An error occurred while loading data. Data integrity is not correct.");
        }
    }
}

