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
     * @see \PHPBackend\Dao\DefaultDAOInterface::create()
     */
    public function create($entity) : void
    {
        throw new DAOException("Operation not supported");
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id) : void
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
        $id = UtilitaireSQL::insert($api, $this->getTableName(), [            
            "title" => $entity->getTitle(),
            "description" => $entity->getDescription(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

}

