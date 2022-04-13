<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\AuxiliaryStock;
use Core\Shivalik\Entities\Product;
use Core\Shivalik\Entities\Stock;
use Core\Shivalik\Managers\StockDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class StockDAOManagerImplementation1 extends DefaultDAOInterface implements StockDAOManager
{
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::hasView()
     */
    protected function hasView(): bool {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::load()
     */
    public function load ($stock): Stock {
        $data = ($stock instanceof Stock) ? $stock : $this->findById(intval($stock, 10));
        if ($this->getDaoManager()->getManagerOf(AuxiliaryStock::class)->checkByParent($data->getId())) {
            $data->setAuxiliaries($this->getDaoManager()->getManagerOf(AuxiliaryStock::class)->findByParent($data->getId()));
        }
        return $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findById()
     */
    public function findById ($id, bool $forward = true) {
        $stock = parent::findById($id, $forward);
        if ($forward && $stock instanceof Stock) {
            $stock->setProduct($this->getDaoManager()->getManagerOf(Product::class)->findById($stock->product->id, false));
        }
        return $stock;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::checkByProduct()
     */
    public function checkByProduct(int $productId, ?bool $empty = null, ?int $limit = null, int $offset = 0): bool {
        $return = false;
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE product = :product".($empty !== null? ' AND served'.($empty? " = " : ' <> ').'quantity' : (''));
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}", ['product' => $productId]);
            if ($statement->fetch()){
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
     * @see \Core\Shivalik\Managers\StockDAOManager::checkByStatus()
     */
    public function checkByStatus(bool $empty = false, ?int $limit = null, int $offset = 0): bool
    {
        $return = false;
        try {
            $SQL = "SELECT * FROM {$this->getViewName()}".($empty !== null? ' WHERE served'.($empty? " = quantity" : ' != quantity OR served IS NULL').'' : (''));
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}");
            if ($statement->fetch()){
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
     * @see \Core\Shivalik\Managers\StockDAOManager::findByProduct()
     */
    public function findByProduct(int $productId, ?bool $empty = null, ?int $limit =  null, int $offset = 0): array
    {
        $data = [];
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE product = :product".($empty !== null? ' AND served'.($empty? " = quantity" : ' != quantity OR served IS NULL').'' : (''));
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}", ['product' => $productId]);
            $class = $this->getMetadata()->getName();
            while ($row = $statement->fetch()){
                $data[] = new $class($row);
            }
            $statement->closeCursor();
            
            if (empty($data)) {
                throw new DAOException("Non data matched by selection query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::findByStatus()
     */
    public function findByStatus(?bool $empty = false, ?int $limit = null, int $offset = 0): array {
        $data = [];
        try {
            $SQL = "SELECT * FROM {$this->getViewName()}".($empty !== null? ' WHERE served'.($empty? " = quantity" : ' != quantity OR served IS NULL').'' : (''));
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}");
            $class = $this->getMetadata()->getName();
            while ($row = $statement->fetch()){
                $stock = new $class($row);
                if ($stock instanceof Stock) {//verification, pour eviter l'exception qui serait lever par la classe AuxiliaryStock
                    $stock->setProduct($this->getDaoManager()->getManagerOf(Product::class)->findById($stock->getProduct()->getId()));
                }
                $data[] = $stock;
            }
            $statement->closeCursor();
            
            if (empty($data)) {
                throw new DAOException("Non data matched by selection query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::countByProduct()
     */
    public function countByProduct(int $productId, ?bool $empty = false): int {
        $count = 0;
        try {
            $SQL = "SELECT COUNT(*) AS nombre FROM {$this->getViewName()} WHERE product=:product".($empty !== null? ' AND served'.($empty? " = quantity" : ' != quantity OR served IS NULL').'' : (''));
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['product' => $productId]);
            if ($row = $statement->fetch()){
                $count = $row['nombre'];
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $count;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::loadAll()
     * @return Stock[]
     */
    public function loadAll(?int $limit = null, int $offset = 0): array
    {
        $stocks = $this->findAll($limit, $offset);
        foreach ($stocks as $stock) {
            $this->load($stock);
        }
        return $stocks;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::loadByProduct()
     * @return Stock[]
     */
    public function loadByProduct(int $productId, ?bool $empty = null, int $limit = null, int $offset = 0): array
    {
        $stocks = $this->findByProduct($productId, $empty, $limit, $offset);
        foreach ($stocks as $stock) {
            $this->load($stock);
        }
        return $stocks;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::loadByStatus()
     */
    public function loadByStatus($empty = false, ?int $limit = null, int $offset = 0): array
    {
        $stocks = $this->findByStatus($empty, $limit, $offset);
        foreach ($stocks as $stock) {
            $this->load($stock);
        }
        return $stocks;
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
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'expiryDate' => $entity->getExpiryDate()->format('Y-m-d'),
            'manufacturingDate' => $entity->getManufacturingDate()->format('Y-m-d')
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
            'unitPrice' => $entity->getUnitPrice(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif(),
            'expiryDate' => $entity->getExpiryDate()->format('Y-m-d'),
            'manufacturingDate' => $entity->getManufacturingDate()->format('Y-m-d')
        ], $id);
    }


}

