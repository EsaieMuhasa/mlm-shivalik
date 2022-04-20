<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\AuxiliaryStock;
use Core\Shivalik\Entities\Stock;
use Core\Shivalik\Managers\AuxiliaryStockDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class AuxiliaryStockDAOManagerImplementation1 extends StockDAOManagerImplementation1 implements AuxiliaryStockDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\StockDAOManagerImplementation1::load()
     * @return AuxiliaryStock
     */
    public function load($stock): Stock {
        $data = ($stock instanceof AuxiliaryStock) ? $stock : $this->findById(intval($stock, 10), true);
        $data->setParent($this->getDaoManager()->getManagerOf(Stock::class)->findById($data->getParent()->getId(), true));
        
        return $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\StockDAOManagerImplementation1::createInTransaction()
     * @param AuxiliaryStock $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void {
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
     * @see \Core\Shivalik\Managers\Implementation\StockDAOManagerImplementation1::update()
     * @param AuxiliaryStock $entity
     */
    public function update($entity, $id): void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'quantity' => $entity->getQuantity(),
            'parent' => $entity->getParent()->getId(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ], $id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::checkByOffice()
     */
    public function checkByOffice(int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): bool {
        $return = false;
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE office =:office";
            $SQL .= ($empty !== null? ' AND (served'.($empty? " = quantity" : " = quantity OR served IS NULL ").')' : (''));
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}", ['office' => $officeId]);
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
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::checkByProductInOffice()
     */
    public function checkByProductInOffice(int $productId, int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): bool {
        $return = false;
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE office =:office AND product =:product ";
            $SQL .= ($empty !== null? ' AND (served'.($empty? " = quantity" : " = quantity OR served IS NULL ").')' : (''));
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}", ['office' => $officeId, 'product' => $productId]);
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
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::countByOffice()
     */
    public function countByOffice(int $officeId, ?bool $empty = null): int {
        $count = 0;
        try {
            $SQL = "SELECT COUNT(*) AS nombre FROM {$this->getViewName()} WHERE office =:office";
            $SQL .= ($empty !== null? ' AND (served'.($empty? " = quantity" : " = quantity OR served IS NULL ").')' : (''));
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['office' => $officeId]);
            if ($row = $statement->fetch()) {
                $count =$row['nombre'];
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $count;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::countByProductInOffice()
     */
    public function countByProductInOffice(int $productId, int $officeId, ?bool $empty = null): int {
        $count = 0;
        try {
            $SQL = "SELECT COUNT(*) AS nombre FROM {$this->getViewName()} WHERE office =:office AND product =:product ";
            $SQL .= ($empty !== null? ' AND (served'.($empty? " = quantity" : " = quantity OR served IS NULL ").')' : (''));
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['office' => $officeId, 'product' => $productId]);
            if ($row = $statement->fetch()) {
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
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::findByOffice()
     */
    public function findByOffice(int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): array {
        $data = [];
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE office =:office";
            $SQL .= ($empty !== null? ' AND (served'.($empty? " = quantity" : " = quantity OR served IS NULL ").')' : (''))." ORDER BY product, dateAjout DESC";
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}", ['office' => $officeId]);
            while ($row = $statement->fetch()) {
                $data[] = new AuxiliaryStock($row);
            }
            $statement->closeCursor();
            
            if (empty($data)) {
                throw new DAOException("No data matched at this selection query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::findByProductInOffice()
     */
    public function findByProductInOffice(int $productId, int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): array {
        $data = [];
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE office =:office AND product =:product ";
            $SQL .= ($empty !== null? ' AND (served'.($empty? " = quantity" : " = quantity OR served IS NULL ").')' : (''));
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}", ['office' => $officeId, 'product' => $productId]);
            while ($row = $statement->fetch()) {
                $data[] = new AuxiliaryStock($row);
            }
            $statement->closeCursor();
            
            if (empty($data)) {
                throw new DAOException("No data matched by this selection query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $data;
    }
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::checkByParent()
     */
    public function checkByParent(int $parentId, ?int $officeId = null, ?bool $empty = null, int $limit = null, int $offset = 0): bool {
        $return = false;
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE parent =:parent".($officeId!=null? " AND office = {$officeId}":'');
            $SQL .= ($empty !== null? ' AND (served'.($empty? " = quantity" : " = quantity OR served IS NULL ").')' : (''));
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}", ['parent' => $parentId]);
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
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::countByParent()
     */
    public function countByParent(int $parentId, ?int $officeId = null, ?bool $empty = null): int {
        $count = 0;
        try {
            $SQL = "SELECT COUNT(*) AS nombre FROM {$this->getViewName()} WHERE parent =:parent".($officeId!=null? " AND office = {$officeId}":'');
            $SQL .= ($empty !== null? ' AND (served'.($empty? " = quantity" : " = quantity OR served IS NULL ").')' : (''));
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['parent' => $parentId]);
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
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::findByParent()
     */
    public function findByParent(int $parentId, ?int $officeId = null, ?bool $empty = null, int $limit = null, int $offset = 0): array {
        $data = [];
        try {
            $SQL = "SELECT * FROM {$this->getViewName()} WHERE parent =:parent".($officeId!=null? " AND office = {$officeId}":'');
            $SQL .= ($empty !== null? ' AND (served'.($empty? " = quantity" : " = quantity OR served IS NULL ").')' : (''));
            $SQL_LIMIT = $limit !== null? "LIMIT {$limit} OFFSET {$offset}":'';
            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), "{$SQL} {$SQL_LIMIT}", ['parent' => $parentId]);
            if ($row = $statement->fetch()) {
                $parent = $this->getDaoManager()->getManagerOf(Stock::class)->findById($parentId);
                
                $stock = new AuxiliaryStock($row);
                $stock->setParent($parent);
                $data[] = $stock;
                while ($row = $statement->fetch()) {
                    $stock = new AuxiliaryStock($row);
                    $stock->setParent($parent);
                    $data[] = $stock;
                }
                $statement->closeCursor();
            }
            
            if (empty($data)) {
                throw new DAOException("No data matched at this selection query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::loadByOffice()
     */
    public function loadByOffice(int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): array {
        $stocks = $this->findByOffice($officeId, $empty, $limit, $offset);
        foreach ($stocks as $stock) {
            $this->load($stock);
        }
        return $stocks;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::loadByParent()
     */
    public function loadByParent(int $parentId, int $officeId = null, ?bool $empty = null, ?int $limit = null, int $offset = 0): array {
        $stocks = $this->findByParent($parentId, $officeId, $empty, $limit, $offset);
        foreach ($stocks as $stock) {
            $this->load($stock);
        }
        return $stocks;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AuxiliaryStockDAOManager::loadByProductInOffice()
     */
    public function loadByProductInOffice(int $productId, int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0): array {
        $stocks = $this->findByProductInOffice($productId, $officeId, $empty, $limit, $offset);
        foreach ($stocks as $stock) {
            $this->load($stock);
        }
        return $stocks;
    }

}

