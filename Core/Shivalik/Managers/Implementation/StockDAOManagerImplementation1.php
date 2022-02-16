<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Stock;
use Core\Shivalik\Managers\StockDAOManager;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class StockDAOManagerImplementation1 extends StockDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::checkByProduct()
     */
    public function checkByProduct(int $productId, ?bool $empty = null): bool
    {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['product' => $productId], 1);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::checkByStatus()
     */
    public function checkByStatus(bool $empty = false, ?int $limit = null, int $offset = 0): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::findByProduct()
     */
    public function findByProduct(int $productId, ?bool $empty = null): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::findByStatus()
     */
    public function findByStatus(?bool $empty = false, ?int $limit = null, int $offset = 0): array
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Stock $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($this->getConnection(), $this->getTableName(), [
            "product" => $entity->getProduct()->getId(),
            'comment' => $entity->getComment(),
            'quantity'=> $entity->getQuantity(),
            'unitPrice' => $entity->getUnitPrice() ,
            'dateAjout' => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Stock $entity
     */
    public function update($entity, $id): void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            "product" => $entity->getProduct()->getId(),
            'comment' => $entity->getComment(),
            'quantity'=> $entity->getQuantity(),
            'unitPrice' => $entity->getUnitPrice() ,
            'dateModif' => $entity->getFormatedDateModif()
        ], $id);
    }


}

