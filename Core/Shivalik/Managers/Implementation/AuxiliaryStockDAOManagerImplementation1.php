<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\AuxiliaryStock;
use Core\Shivalik\Managers\AuxiliaryStockDAOManager;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class AuxiliaryStockDAOManagerImplementation1 extends StockDAOManagerImplementation1 implements AuxiliaryStockDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\StockDAOManagerImplementation1::checkByProduct()
     */
    public function checkByProduct(int $productId, ?bool $empty = null, ?int $limit = null, int $offset = 0): bool
    {
        $return = false;
        try {
            //comptage pour savoie si le stock est vide ou pas
            $sql = "SELECT * FROM {$this->getTableName()} WHERE parent IN (SELECT id FROM Stock WHERE product = :product) ".($limit !== null? "LIMIT {$limit} OFFSET {$offset}":'');
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $sql, ['product' => $productId]);
            if ($statement->fetch()) {
                $return = true;
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\StockDAOManagerImplementation1::checkByStatus()
     */
    public function checkByStatus(bool $empty = false, int $limit = null, int $offset = 0): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\StockDAOManagerImplementation1::createInTransaction()
     * @param AuxiliaryStock $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'parent' => $entity->getParent()->getId(),
            'office' => $entity->getOffice()->getId(),
            'quantity' => $entity->getQuantity()
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\StockDAOManagerImplementation1::findByProduct()
     */
    public function findByProduct(int $productId, ?bool $empty = null, ?int $limit = null, int $offset = 0): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\StockDAOManagerImplementation1::findByStatus()
     */
    public function findByStatus(bool $empty = false, int $limit = null, int $offset = 0): array
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\StockDAOManagerImplementation1::update()
     */
    public function update($entity, $id): void
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::checkByOffice()
     */
    public function checkByOffice(int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): bool
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::checkByProductInOffice()
     */
    public function checkByProductInOffice(int $productId, int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): bool
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::countByOffice()
     */
    public function countByOffice(int $officeId, ?bool $empty = null): int
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::countByProductInOffice()
     */
    public function countByProductInOffice(int $productId, int $officeId, ?bool $empty = null): int
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::findByOffice()
     */
    public function findByOffice(int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): array
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::findByProductInOffice()
     */
    public function findByProductInOffice(int $productId, int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): array
    {
        // TODO Auto-generated method stub
        
    }
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::checkByParent()
     */
    public function checkByParent(int $parentId, ?int $officeId = null, ?bool $empty = null, int $limit = null, int $offset = 0): bool
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::countByParent()
     */
    public function countByParent(int $parentId, ?int $officeId = null, ?bool $empty = null): bool
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::findByParent()
     */
    public function findByParent(int $parentId, ?int $officeId = null, ?bool $empty = null, int $limit = null, int $offset = 0): array
    {
        // TODO Auto-generated method stub
        
    }



}

