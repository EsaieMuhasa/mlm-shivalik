<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Generation;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\MonthlyOrder;
use Core\Shivalik\Entities\NotificationReceiver;
use Core\Shivalik\Entities\PointValue;
use Core\Shivalik\Entities\PurchaseBonus;
use Core\Shivalik\Managers\MonthlyOrderDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Managers\MemberDAOManager;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class MonthlyOrderDAOManagerImplementation1 extends AbstractOperationDAOManager implements MonthlyOrderDAOManager
{
    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::hasView()
     */
    protected function hasView(): bool {
        return true;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::dispatchPurchaseBonus()
     */
    public function dispatchPurchaseBonus(): void {
        $date  = new \DateTime();
        $befor = (clone $date)->modify('-1 month');
        
        $dateMin = "{$befor->format('T-m')}-28 01:00:00";
        $dateMax = "{$date->format('Y-m')}-28 23:59:59";
        
        $SQL = "SELECT * FROM {$this->getViewName()} WHERE (dateAjout BETWEEN  :dateMin AND :dateMax) AND disabilityDate IS NULL";
        
        $mothlyOrders = [];
        $pdo = $this->getConnection();
        try {
            
            if (!$pdo->beginTransaction()) {
                throw new DAOException("Impossible to perform this operation because an error occured in starting transaction process");
            }
            
            $statement = UtilitaireSQL::prepareStatement($pdo, $SQL, ['dateMin' => $dateMin, 'dateMax' => $dateMax]);
            while ($row = $statement->fetch()) {
                $order = new MonthlyOrder($row);
                $order->setMember($this->memberDAOManager->findById($order->getMember()->getId()));
                $mothlyOrders[] = $order; 
            }
            $statement->closeCursor();
        
            foreach ($mothlyOrders as $order) {
                //desactivation du compte memsuel
                UtilitaireSQL::update($pdo, $this->getTableName(), ['disabilityDate' => $date->format('Y-m-d H:i:s')], $order->getId());
                
                if($order->getAvailable() == 0){
                    continue;
                }
                
                //bonus reachat et PV
                $member = $order->getMember();
                $generator = $this->getManagerFactory()->getManagerOf(GradeMember::class)->findCurrentByMember($member->getId());
                $bonus = new PurchaseBonus();
                $pv = new PointValue();
                $now = new \DateTime();
                
                $notificationReceivers = [];
                $pointValues = [];
                $purchaseBonus = [];
                
                $bonus->setGenerator($generator);
                $bonus->setMember($member);
                $bonus->setAmount(($order->getAvailable() / 100) * 15);
                $bonus->setDateAjout($now);
                $bonus->setMonthlyOrder($order);
                
                $value = round(($order->getAmount()/2), 0);
                $pv->setGenerator($generator);
                $pv->setMember($member);
                $pv->setFoot(null);
                $pv->setValue($value);
                $pv->setMonthlyOrder($order);
                
                $purchaseBonus[] = $bonus;
                $pointValues[] = $pv;
                
                if ($member->getParent() != null) {
                    
                    $parent = $order->getMember();
                    $generationNumber = Generation::MIN_GENERATION;
                    
                    $amountBonusGeneration = ($order->getAmount()/100);
                    $amountBonusGeneration = round($amountBonusGeneration, 2, PHP_ROUND_HALF_DOWN);
                    
                    while ($this->memberDAOManager->checkParent($parent->getId())) {
                        $foot = $parent->getFoot();//les PV sont appercue au pied du parent, identifier par le foot du fils de sont arbre
                        $parent = $this->memberDAOManager->findParent($parent->getId());
                        
                        $pv = new PointValue();
                        $pv->setGenerator($generator);
                        $pv->setMember($parent);
                        $pv->setFoot($foot);
                        $pv->setValue($value);
                        $pv->setMonthlyOrder($order);
                        
                        if ($generationNumber <= 15) {//bonus jusqu'au 15 em generation
                            
                            $bonus = new PurchaseBonus();
                            $bonus->setGenerator($generator);
                            $bonus->setMember($parent);
                            $bonus->setMonthlyOrder($order);
                            $bonus->setAmount($amountBonusGeneration);
                            $bonus->setDateAjout($now);
                            $bonus->setGeneration($generationNumber);
                            
                            $title = "Purchase bonus";
                            $description = "Congratulations {$parent->getNames()}. You got $ {$amountBonusGeneration} bonus, for your downline {$member->getMatricule()} account purchase bonus.";
                            $notificationReceivers[] = NotificationReceiver::buildNotificationReceiver($title, $description, $parent);
                            $purchaseBonus[] = $bonus;
                            
                            $generationNumber++;
                        }
                        
                        $title = "PV purchase bonus";
                        $description = "Congratulations {$parent->getNames()}. You got  {$pv->getValue()} PV, for your downline {$member->getMatricule()} account, purchase bonus";
                        $notificationReceivers[] = NotificationReceiver::buildNotificationReceiver($title, $description, $parent);
                        $pointValues[]= $pv;
                        
                    }
                }
                foreach ($purchaseBonus as $bn) {
                    $this->getDaoManager()->getManagerOf(PurchaseBonus::class)->createInTransaction($bn, $pdo);
                }
                foreach ($pointValues as $point) {
                    $this->getDaoManager()->getManagerOf(PointValue::class)->createInTransaction($point, $pdo);
                }
                foreach ($notificationReceivers as $receiver) {
                    $this->getDaoManager()->getManagerOf(NotificationReceiver::class)->createInTransaction($receiver, $pdo);
                }
            }
            
            if (!$pdo->commit()) {
                throw new DAOException("Failed to execute the operation because an error occurred while closing the transaction");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::checkByMonth()
     */
    public function checkByMonth (?int $month = null, ?int $year = null, ?bool $status = null, int $offset = 0): bool {
        if ($month === null || $year === null) {
            $now = new \DateTime();
            $month = $month == null? intval($now->format('m'), 10) : $month;
            $year = $year == null? intval($now->format('Y'), 10) : $year;
        }
        
        $m = ($month < 10? "0":"").$month;
        $date  = new \DateTime("01-{$m}-{$year}");
        $befor = (clone $date)->modify('-1 month');
        
        $dateMin = "28-{$befor->format('Y-m')} 01:00:00";
        $dateMax = "{$date->format('Y-m')}-28 23:59:59";
        
        $SQL_STATUS = $status !== null? ("AND disabilityDate IS ". ($status? '':'NOT ')."NULL") : ("");
        $SQL = "SELECT * FROM {$this->getViewName()} WHERE (dateAjout BETWEEN :dateMin AND :dateMax) {$SQL_STATUS} LIMIT 1 OFFSET {$offset}";
        $return = false;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['dateMin' => $dateMin, 'dateMax' => $dateMax]);
            if ($statement->fetch()) {
                $return = true;
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage()." {$SQL}", DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::countByMonth()
     */
    public function countByMonth(?int $month = null, ?int $year = null, ?bool $status = null): int {
        if ($month === null || $year === null) {
            $now = new \DateTime();
            $month = $month == null? intval($now->format('m'), 10) : $month;
            $year = $year == null? intval($now->format('Y'), 10) : $year;
        }
        
        $m = ($month < 10? "0":"").$month;
        $date  = new \DateTime("01-{$m}-{$year}");
        $befor = (clone $date)->modify('-1 month');
        
        $dateMin = "28-{$befor->format('Y-m')} 01:00:00";
        $dateMax = "{$date->format('Y-m')}-28 23:59:59";
        
        $SQL_STATUS = $status !== null? ("AND disabilityDate IS ". ($status? '':'NOT ')."NULL") : ("");
        $SQL = "SELECT COUNT(*) AS nombre FROM {$this->getViewName()} WHERE (dateAjout BETWEEN :dateMin AND :dateMax) {$SQL_STATUS}";
        $return = 0;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['dateMin' => $dateMin, 'dateMax' => $dateMax]);
            if ($row = $statement->fetch()) {
                $return = $row['nombre'];
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage()." {$SQL}", DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::findByMonth()
     */
    public function findByMonth(?int $month = null, ?int $year = null, ?bool $status = null, ?int $limit = null, int $offset = 0): array {
        if ($month === null || $year === null) {
            $now = new \DateTime();
            $month = $month == null? intval($now->format('m'), 10) : $month;
            $year = $year == null? intval($now->format('Y'), 10) : $year;
        }
        
        $m = ($month < 10? "0":"").$month;
        $date  = new \DateTime("01-{$m}-{$year}");
        $befor = (clone $date)->modify('-1 month');
        
        $dateMin = "28-{$befor->format('Y-m')} 01:00:00";
        $dateMax = "{$date->format('Y-m')}-28 23:59:59";
        
        $SQL_STATUS = $status !== null? ("AND disabilityDate IS ". ($status? '':'NOT ')."NULL") : ("");
        $SQL_LIMIT = $limit !== null? ("LIMIT {$limit} OFFSET {$offset}") : ("");
        $SQL = "SELECT * FROM {$this->getViewName()} WHERE (dateAjout BETWEEN :dateMin AND :dateMax) {$SQL_STATUS} {$SQL_LIMIT}";
        $data = [];
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['dateMin' => $dateMin, 'dateMax' => $dateMax]);
            while ($row = $statement->fetch()) {
                $data[] = new MonthlyOrder($row);
            }
            $statement->closeCursor();
            if (empty($data)) {
                throw  new DAOException("no data matched by this query in database");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage()." {$SQL}", DAOException::ERROR_CODE, $e);
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
     * @param MonthlyOrder $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void {
        if (!$this->checkByMemberOfMonth($entity->getMember()->getId(), 
            intval($entity->getDateAjout()->format('m'), 10), intval($entity->getDateAjout()->format('Y'), 10))) {
            $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
                'member' => $entity->getMember()->getId(),
                self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
            ]);
            $entity->setId($id);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::checkByMemberOfMonth()
     */
    public function checkByMemberOfMonth(int $memberId, ?int $month=null, ?int $year=null): bool {
        
        if ($month === null || $year === null) {
            $now = new \DateTime();
            $month = $month == null? intval($now->format('m'), 10) : $month;
            $year = $year == null? intval($now->format('Y'), 10) : $year;
        }
        
        $m = ($month < 10? "0":"").$month;
        $date  = new \DateTime("01-{$m}-{$year}");
        $befor = (clone $date)->modify('-1 month');
        
        $dateMin = "28-{$befor->format('Y-m')} 01:00:00";
        $dateMax = "{$date->format('Y-m')}-28 23:59:59";
        
        $SQL = "SELECT * FROM {$this->getTableName()} WHERE member = {$memberId} AND (dateAjout BETWEEN :dateMin AND :dateMax) LIMIT 1";
        $return = false;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['dateMin' => $dateMin, 'dateMax' => $dateMax]);
            if ($statement->fetch()) {
                $return = true;
            }
            
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage()." {$SQL}", DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::findByMemberOfMonth()
     */
    public function findByMemberOfMonth(int $memberId, ?int $month = null, ?int $year = null): MonthlyOrder {
        if ($month === null || $year === null) {
            $now = new \DateTime();
            $month = $month == null? intval($now->format('m'), 10) : $month;
            $year = $year == null? intval($now->format('Y'), 10) : $year;
        }
        
        $m = ($month < 10? "0":"").$month;
        $date  = new \DateTime("01-{$m}-{$year}");
        $befor = (clone $date)->modify('-1 month');
        
        $dateMin = "{$befor->format('T-m')}-28 01:00:00";
        $dateMax = "{$date->format('Y-m')}-28 23:59:59";
        
        $SQL = "SELECT * FROM {$this->getViewName()} WHERE member = {$memberId} AND (dateAjout BETWEEN  :dateMin AND :dateMax) LIMIT 1";
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, ['dateMin' => $dateMin, 'dateMax' => $dateMax]);
            if ($row = $statement->fetch()) {
                $statement->closeCursor();
                return new MonthlyOrder($row);
            } 
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        throw new DAOException("no month order started in member account indexed by {$memberId}");
    }

}

