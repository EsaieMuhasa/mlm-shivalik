<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Notification;
use Core\Shivalik\Managers\NotificationDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class NotificationDAOManagerImplementation1 extends NotificationDAOManager
{
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Notification $entity
     */
    public function createInTransaction($entity, $api): void
    {
        if (!$api->inTransaction()) {
            throw new DAOException("A transaction must be started in advance");
        }
        $id = UtilitaireSQL::insert($this->getTableName(), $this->getTableName(), [            
            "title" => $entity->getTitle(),
            "description" => $entity->getDescription(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

}

