<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Notification;
use Core\Shivalik\Managers\NotificationDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class NotificationDAOManagerImplementation1 extends DefaultDAOInterface implements NotificationDAOManager
{
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::create()
     */
    public function create($entity)
    {
        throw new DAOException("Operation not supported");
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id)
    {
        throw new DAOException("Operation not supported");
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
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

