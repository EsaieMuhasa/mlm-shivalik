<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Generation;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\MonthlyOrder;
use Core\Shivalik\Entities\NotificationReceiver;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\PointValue;
use Core\Shivalik\Entities\PurchaseBonus;
use Core\Shivalik\Managers\MonthlyOrderDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Managers\MemberDAOManager;
use DateTime;

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
                
                if($order->getAvailable() == 0 || $order->getDisabilityDate() != null){
                    continue;
                }
                
                //desactivation du compte memsuel
                $order->setDisabilityDate($date);
                UtilitaireSQL::update($pdo, $this->getTableName(), ['disabilityDate' => $date->format('Y-m-d H:i:s')], $order->getId());
                
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
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::dispatchManualPurchaseBonus()
     */
    public function dispatchManualPurchaseBonus(MonthlyOrder $order): void {
        $now  = new \DateTime();
        try {
            $pdo = $this->getConnection();
            
            if (!$pdo->beginTransaction()) {
                throw new DAOException("Impossible to perform this operation because an error occured in starting transaction process");
            }

            //check if user has other monthly bonus
            $month = intval($now->format('m'), 10);
            $year = intval($now->format('Y'), 10);
            if ($this->checkByMemberOfMonth($order->getMember()->getId(), null, $month, $year)){
                $message = "Impossible to perform this operation. the same member account cannot obtain 2 repurchase bonuses for the same month. ";
                $message .= "This operation could be carried out the following month. Thank you for the confidence you have in favor of the Shivalick company.";
                throw new DAOException($message);
            }
            
            //insert monthly order in database
            $order->setDateAjout($now);
            $order->setDisabilityDate($now);
            $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
                'member' => $order->getMember()->getId(),
                self::FIELD_DATE_AJOUT => $order->getFormatedDateAjout(),
                'disabilityDate' => $order->getDisabilityDate()->format('Y-m-d H:i:s'),
                'manualAmount' => $order->getManualAmount(),
                'office' => $order->getOffice()->getId()
            ]);
            $order->setId($id);
            //==
            
            //purchase bonas end dispatching PVs
            $member = $order->getMember();
            $generator = $this->getManagerFactory()->getManagerOf(GradeMember::class)->findCurrentByMember($member->getId());
            $bonus = new PurchaseBonus();
            $pv = new PointValue();
            
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
            
            if (!$pdo->commit()) {
                throw new DAOException("Failed to execute the operation because an error occurred while closing the transaction");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::checkManualBurchaseBonusByOffice()
     */
    public function checkManualBurchaseBonusByOffice (int $officeId, ?int $limit = null, int $offset = 0): bool {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['office' => $officeId], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::findManualBurchaseBonusByOffice()
     */
    public function findManualBurchaseBonusByOffice (int $officeId, ?int $limit = null, int $offset = 0): array {
        $orders = UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, ['office' => $officeId], $limit, $offset);
        foreach ($orders as $order) {
            $order->setMember($this->memberDAOManager->findById($order->getMember()->getId()));
        }
        return $orders;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::countManualBurchaseBonusByOffice()
     */
    public function countManualBurchaseBonusByOffice(int $officeId): int {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), ['office' => $officeId]);
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
        $now = ($entity->getDateAjout() == null)? new DateTime() : $entity->getDateAjout();
        $month = intval($now->format('m'), 10);
        $year = intval($now->format('Y'), 10);
        if ($this->checkByMemberOfMonth($entity->getMember()->getId(), false, $month, $year)){
            $message = "Impossible to perform this operation. the same member account cannot obtain 2 repurchase bonuses for the same month. ";
            $message .= "This operation could be carried out the following month. Thank you for the confidence you have in favor of the Shivalick company.";
            throw new DAOException($message);
        }

        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'member' => $entity->getMember()->getId(),
            'manualAmount' => $entity->getManualAmount(),
            'office' => $entity->getOffice()->getId(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

    public function buildByMemberOfMonth (int $memberId, ?Office $office = null) : MonthlyOrder {
        $now = new DateTime();
        $month = intval($now->format('m'), 10);
        $year = intval($now->format('Y'), 10);
        if ($this->checkByMemberOfMonth($memberId, false, $month, $year)) {
            return $this->findByMemberOfMonth($memberId, false, $month, $year);
        }

        if($office == null) {
            throw new DAOException('This operation cannot be continued because the office responsible for this operation is null');
        }

        $order = new MonthlyOrder([
            'office' => $office,
            'member' => $memberId,
            self::FIELD_DATE_AJOUT => $now
        ]);

        $this->createInTransaction($order, $this->getConnection());
        return $order;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MonthlyOrderDAOManager::checkByMemberOfMonth()
     */
    public function checkByMemberOfMonth(int $memberId, ?bool $dispatched = false, ?int $month=null, ?int $year=null): bool {
        
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
        
        $END_AND = $dispatched !== null? (' AND disabilityDate IS '.($dispatched? 'NOT ' : '').'NULL') : '';
        $SQL = "SELECT * FROM {$this->getTableName()} WHERE member = {$memberId}{$END_AND} AND (dateAjout BETWEEN :dateMin AND :dateMax) LIMIT 1";
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
    public function findByMemberOfMonth(int $memberId, ?bool $dispatched = false, ?int $month = null, ?int $year = null): MonthlyOrder {
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
        
        $END_AND = $dispatched !== null? (' AND disabilityDate IS '.($dispatched? 'NOT ' : '').'NULL') : '';
        $SQL = "SELECT * FROM {$this->getViewName()} WHERE member = {$memberId}{$END_AND} AND (dateAjout BETWEEN  :dateMin AND :dateMax) LIMIT 1";
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

