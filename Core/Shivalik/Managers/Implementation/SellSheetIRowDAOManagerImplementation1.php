<?php

namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\MonthlyOrder;
use Core\Shivalik\Entities\SellSheetIRow;
use Core\Shivalik\Managers\SellSheetIRowDAOManager;
use PDO;
use PDOException;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

class SellSheetIRowDAOManagerImplementation1 extends DefaultDAOInterface implements SellSheetIRowDAOManager {

    /**
     * @param SellSheetIRow $entity
     * @param PDO $pdo
     * @return void
     */
    public function createInTransaction($entity, PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'product' => $entity->getProduct()->getId(),
            'monthlyOrder' => $entity->getMonthlyOrder()->getId(),
            'dateAjout' => $entity->getFormatedDateAjout(),
            'quantity' => $entity->getQuantity(),
            'unitPrice' => $entity->getUnitPrice()
        ]);
        $entity->setId($id);
    }

    public function update($entity, $id): void
    {
        throw new DAOException('Update operation are not support for all salle sheet row');
    }

    public function checkByMonthlyOrder (int $monthlyOrder) : bool {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['monthlyOrder' => $monthlyOrder]);
    }
    
    public function findByMonthlyOrder (int $monthlyOrder) : array{
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, ['monthlyOrder' => $monthlyOrder]);
    }
    
    /**
     * @param int[]|MonthlyOrder[] $monthlyOrders
     * @return int
     */
    public function countByMonthlyOrders (array $monthlyOrders) : int{
        if ($monthlyOrders[0] instanceof MonthlyOrder) {
            $params = [];
            foreach ($monthlyOrders as $m) {
                $params[] = $m->getId();
            }
        } else {
            $params = $monthlyOrders;
        }
        $in = join(', ', $params);
        $return = 0;
        try {
            $sql = "SELECT COUNT(*) AS nombre FROM {$this->getTableName()} WHERE monthlyOrder IN({$in})";
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $sql);
            if ($row = $statement->fetch()){
                $return = $row['nombre'];
            }
            $statement->closeCursor();
        } catch (PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }
    
    /**
     * @param int[]|MonthlyOrder[] $monthlyOrders
     * @param int $offset
     * @return boolean
     */
    public function checkByMonthlyOrders (array $monthlyOrders, int $offset = 0) : bool {
        if ($monthlyOrders[0] instanceof MonthlyOrder) {
            $params = [];
            foreach ($monthlyOrders as $m) {
                $params[] = $m->getId();
            }
        } else {
            $params = $monthlyOrders;
        }
        $in = join(', ', $params);
        $return = false;
        try {
            $sql = "SELECT *FROM {$this->getTableName()} WHERE monthlyOrder IN({$in}) LIMIT 1 OFFSET {$offset}";
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $sql);
            if ($statement->fetch()){
                $return = true;
            }
            $statement->closeCursor();
        } catch (PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }
    
    public function findByMonthlyOrders (array $monthlyOrders, ?int $limit = null, int $offset = 0) : array {
        if ($monthlyOrders[0] instanceof MonthlyOrder) {
            $params = [];
            foreach ($monthlyOrders as $m) {
                $params[] = $m->getId();
            }
        } else {
            $params = $monthlyOrders;
        }
        $in = join(', ', $params);
        $return = [];
        try {
            $sql = "SELECT *FROM {$this->getTableName()} WHERE monthlyOrder IN({$in}) LIMIT 1 OFFSET {$offset}";
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $sql);
            while ($row = $statement->fetch()){
                $return[] = new SellSheetIRow($row);
            }
            $statement->closeCursor();

            if (empty($return)){
                throw new DAOException('no sell sheet rows matched monthly orders in database');
            }
        } catch (PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }
}