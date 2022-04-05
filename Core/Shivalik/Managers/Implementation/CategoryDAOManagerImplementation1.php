<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\CategorieDAOManager;
use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class CategorieDAOManagerImplementation1 extends DefaultDAOInterface implements CategorieDAOManager
{
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id): void
    {
        // TODO Auto-generated method stub
        
    }


}

