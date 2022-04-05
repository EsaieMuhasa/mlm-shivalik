<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Stock;
use Core\Shivalik\Managers\StockDAOManager;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\DAOException;
use Core\Shivalik\Entities\AuxiliaryStock;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class StockDAOManagerImplementation1 extends DefaultDAOInterface implements StockDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::checkByProduct()
     */
    public function checkByProduct(int $productId, ?bool $empty = null, ?int $limit = null, int $offset = 0): bool
    {
        $return = false;
        try {
            $sql = "SELECT * FROM {$this->getTableName()} WHERE product = :product ".($limit !== null? "LIMIT {$limit} OFFSET {$offset}":'');
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $sql, ['product' => $productId]);
            while ($row = $statement->fetch()){
                if ($empty !== null) {
                    $stock = new Stock($row);
                    if ($this->getDaoManager()->getManagerOf(AuxiliaryStock::class)->checkByParent($stock->getId())){
                        $stock->setAuxiliaries($this->getDaoManager()->getManagerOf(AuxiliaryStock::class)->findByParent($stock->getId()));
                    }
                    
                    if ($empty) {
                        if ($stock->getSold() == 0 ) {
                            $return = true;
                            break;
                        }
                    } else {
                        if ($stock->getSold() != 0 ) {
                            $return = false;
                            break;
                        }
                    }
                } else {                    
                    $return = true;
                    break;
                }
            }
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
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\StockDAOManager::findByProduct()
     */
    public function findByProduct(int $productId, ?bool $empty = null, ?int $limit =  null, int $offset = 0): array
    {
        $data = [];
        try {
            $sql = "SELECT * FROM {$this->getTableName()} WHERE product = :product ".($limit !== null? "LIMIT {$limit} OFFSET {$offset}":'');
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $sql, ['product' => $productId]);
            while ($row = $statement->fetch()){
                if ($empty !== null) {
                    $stock = new Stock($row);
                    if ($this->getDaoManager()->getManagerOf(AuxiliaryStock::class)->checkByParent($stock->getId())){
                        $stock->setAuxiliaries($this->getDaoManager()->getManagerOf(AuxiliaryStock::class)->findByParent($stock->getId()));
                    }
                    
                    if ($empty) {
                        if ($stock->getSold() == 0 ) {
                            $data[] = $stock;
                        }
                    } else {
                        if ($stock->getSold() != 0 ) {
                            $data[] = $stock;
                        }
                    }
                } else {
                    $data[] = $stock;
                }
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

