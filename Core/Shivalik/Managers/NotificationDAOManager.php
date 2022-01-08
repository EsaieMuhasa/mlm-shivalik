<?php
namespace Core\Shivalik\Managers;

use PHPBackend\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
abstract class NotificationDAOManager extends DefaultDAOInterface
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

}

