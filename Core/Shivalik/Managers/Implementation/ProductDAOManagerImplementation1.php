<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Entities\Product;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class ProductDAOManagerImplementation1 extends DefaultDAOInterface implements ProductDAOManager
{
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Product $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'name' => $entity->getName(),
            'dateAjout' => $entity->getFormatedDateAjout(),
            'defaultUnitPrice' => $entity->getDefaultUnitPrice(),
            'description' => $entity->getDescription()
        ]);
        $entity->setId($id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::findByName()
     */
    public function findByName (string $name) : Product {
        return  $this->findByColumnName('name', $name);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::checkByName()
     */
    public function checkByName (string $name,  ?int $id = null) : bool {
        return  $this->checkByColumnName('name', $name, $id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Product $entity
     */
    public function update($entity, $id): void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'dateModif' => $entity->getFormatedDateModif(),
            'name' => $entity->getName(),
            'defaultUnitPrice' => $entity->getDefaultUnitPrice(),
            'description' => $entity->getDescription()
        ], $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\ProductDAOManager::updatePicture()
     */
    public function updatePicture(string $path, int $id): void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'picture' => $path
        ], $id);
    }
 
}

