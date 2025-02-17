<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Managers\CommandDAOManager;
use PHPBackend\Dao\DefaultDAOInterface;
use Core\Shivalik\Entities\Command;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Entities\ProductOrdered;
use PHPBackend\Dao\DAOException;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Entities\MonthlyOrder;
use Core\Shivalik\Managers\MonthlyOrderDAOManager;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class CommandDAOManagerImplementation1 extends DefaultDAOInterface implements CommandDAOManager {
    
    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::deliver()
     */
    public function deliver (int $id): void {
        $now = new \DateTime();
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'deliveryDate' => $now->format('Y-m-d\TH:i:s')
        ], $id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::load()
     */
    public function load ($command): Command {
        $com = ($command instanceof Command) ? $command : $this->findById(intval($command, 10));
        $com->setMember($this->memberDAOManager->findById($com->getMember()->getId()));
        $com->setProducts($this->getDaoManager()->getManagerOf(ProductOrdered::class)->findByCommand($com->getId()));
        return $com;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
     * @param Command $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void {
        if ($entity->getMonthlyOrder() == null || $entity->getMonthlyOrder()->getId() == null || $entity->getMonthlyOrder()->getId() <= 0) {
            
            $m = intval($entity->getFormatedDateAjout('m'), 10);
            $y = intval($entity->getFormatedDateAjout('Y'), 10);
            
            /**
             * @var MonthlyOrderDAOManager $monthlyDao
             */
            $monthlyDao = $this->getManagerFactory()->getManagerOf(MonthlyOrder::class);
            if($monthlyDao->checkByMemberOfMonth($entity->getMember()->getId(), $m, $y)) {                
                $monthly = $monthlyDao->findByMemberOfMonth($entity->getMember()->getId(), $m, $y);
            } else {
                $monthly = new MonthlyOrder();
                $monthly->setMember($entity->getMember());
                $monthly->setDateAjout($entity->getDateAjout());
                $monthlyDao->createInTransaction($monthly, $pdo);
            }
            
            $entity->setMonthlyOrder($monthly);
        }
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout(),
            'member' => $entity->getMember()->getId(),
            'office' => $entity->getOffice()->getId(),
            'monthlyOrder' => $entity->getMonthlyOrder()->getId(),
            'officeAdmin' => $entity->getOfficeAdmin() != null? $entity->getOfficeAdmin()->getId() : null
        ]);
        
        $entity->setId($id);
        
        foreach ($entity->getProducts() as $pr) {
            $this->getManagerFactory()->getManagerOf(ProductOrdered::class)->createInTransaction($pr, $pdo);
        }
    }
    
    /**
     * verification des operations en filtra sur une collone qui est une cle etrangere dans la table Command
     * @param string $column nom de la collone
     * @param int $value valeur de la clee
     * @param bool $delivered la commande est-elle deja delivrer??
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return bool
     */
    protected function checkByForeignKey (string $column, int $value, ?bool $delivered = null, ?int $limit = null, int $offset = 0) : bool {
        if ($delivered === null) {
            return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), [$column => $value], $limit, $offset);
        }
        
        $SQL = "SELECT * FROM {$this->getTableName()} WHERE {$column} = {$value} AND deliveryDate IS ".($delivered? 'NOT ':'')."NULL";
        $SQL .= $limit !== null? " LIMIT {$limit} OFFSET {$offset}" : " LIMIT 1 OFFSET 0";
        
        $return = false;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL);
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
     * comptage des operations en filtrant sur un collone qui est une clee etrangere dans la table commande
     * @param string $column nom de la collone
     * @param int $value valeur en filtrer
     * @param bool $delivered la commande doit-elle deja ete delivrer??
     * @throws DAOException
     * @return int nombre de commande corresondant au filtre
     */
    protected function countByForeignKey (string $column, int $value, ?bool $delivered = null): int {
        if ($delivered === null) {
            return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), [$column => $value]);
        }
        
        $SQL = "SELECT COUNT(*) AS nombre FROM {$this->getTableName()} WHERE {$column} = {$value} AND deliveryDate IS ".($delivered? 'NOT ':'')."NULL";
        $return = 0;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL);
            if ($row = $statement->fetch()) {
                $return = $row['nombre'];
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }
    
    /**
     * Selection des commandes en filtrant sur une collone qui est une clee etrangere dans la table commande
     * @param string $column nom de la collone
     * @param int $value valeur a filtrer
     * @param bool $delivered la commande doit-elle deja ete delivrer??
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return Command[]
     */
    protected function findByForeignKey (string $column, int $value, ?bool $delivered = null, ?int $limit = null, int $offset = 0) : array {
        if ($delivered === null) {
            $data = UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(),
                self::FIELD_DATE_AJOUT, [$column => $value], $limit, $offset);
            foreach ($data as $c) {
                $c->setMember($this->memberDAOManager->findById($c->getMember()->getId()));
            }
            return $data;
        }
        
        $SQL = "SELECT * FROM {$this->getTableName()} WHERE {$column} = {$value} AND deliveryDate IS ".($delivered? 'NOT ':'')."NULL ORDER BY dateAjout DESC";
        $SQL .= $limit !== null? " LIMIT {$limit} OFFSET {$offset}" : "";
        
        $return = [];
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL);
            while ($row = $statement->fetch()) {
                $c = new Command($row);
                $c->setMember($this->memberDAOManager->findById($c->getMember()->getId()));
                $return[] = $c;
            }
            $statement->closeCursor();
            if (empty($return)) {
                throw new DAOException("No data matched by this selection query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }
    
    /**
     * verification des commandes en une intervale de date ou une date, pour une clee etrangere dans la table command
     * @param string $column nom de la collone
     * @param int $value valeur de filtrage
     * @param \DateTime $min la date min
     * @param \DateTime $max la date max (optionnel)
     * @param bool $delivered la commande est-elle deja livrer??
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return bool
     */
    protected function checkByForeignKeyAtDate(string $column, int $value, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null, ?int $limit = null, int $offset = 0): bool {
        $columnName = self::FIELD_DATE_AJOUT;
        if ($delivered === null) {
            return UtilitaireSQL::hasCreationHistory($this->getConnection(), $this->getTableName(), $columnName, true, $min, $max, [$column => $value], $limit, $offset);
        }
        
        if ($max == null) {
            $max = $min;
        }
        
        $params = [];
        $params['dateMin'] = $min->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $max->format('Y-m-d\T23:59:59');
        $params[$column] = $value;
        
        $SQL = "SELECT * FROM {$this->getTableName()} WHERE {$column} = :{$column} AND deliveryDate IS ".($delivered? 'NOT ':'')."NULL";
        $SQL .= " {$columnName} >= :dateMin AND {$columnName} <=:dateMax ";
        $SQL .= $limit !== null? " LIMIT {$limit} OFFSET {$offset}" : " LIMIT 1 OFFSET 0";
        
        $return = false;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, $params);
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
     * comptage des commandes en une intervale des dates ou en une date pour une clee etrange dans la table command
     * @param string $column nom de la collone
     * @param int $value valeur de filtrage
     * @param \DateTime $min la date min
     * @param \DateTime $max la date max (optionnel)
     * @param bool $delivered la commande est-elle deja livrer??
     * @throws DAOException
     * @return int
     */
    protected function countByForeignKeyAtDate(string $column, int $value, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null): int {
        $columnName = self::FIELD_DATE_AJOUT;
        if ($delivered === null) {
            return UtilitaireSQL::countByCreationHistory($this->getConnection(), $this->getTableName(), $columnName, true, $min, $max, [$column => $value]);
        }
        
        if ($max == null) {
            $max = $min;
        }
        
        $params = [];
        $params['dateMin'] = $min->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $max->format('Y-m-d\T23:59:59');
        $params[$column] = $value;
        
        $SQL = "SELECT COUNT(*) AS nombre FROM {$this->getTableName()} WHERE {$column} = :{$column} AND deliveryDate IS ".($delivered? 'NOT ':'')."NULL";
        $SQL .= " {$columnName} >= :dateMin AND {$columnName} <=:dateMax ";
        
        $return = 0;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, $params);
            if ($row = $statement->fetch()) {
                $return = $row['nombre'];
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }
    
    /**
     * selection des commandes faites en une intervalle des dates ou en une date pour un clee etrangere dans la table commande
     * @param string $column le nom de la collone
     * @param int $value la valeur en prendre en compte pour le filtrage
     * @param \DateTime $min la date min ou la date du jour
     * @param \DateTime $max la date max (optionnel). date le cas où il veau null alors max = min
     * @param bool $delivered la commande doit-elle estre deja livrer???
     * @param int $limit
     * @param int $offset
     * @throws DAOException
     * @return Command[]
     */
    protected function findByForeignKeyAtDate(string $column, int $value, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null, ?int $limit = null, int $offset = 0): array {
        $columnName = self::FIELD_DATE_AJOUT;
        if ($delivered === null) {
            $data = UtilitaireSQL::findCreationHistory($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), 
                $columnName, true, $min, $max, [$column => $value], $limit, $offset);
            foreach ($data as $c) {
                $c->setMember($this->memberDAOManager->findById($c->getMember()->getId()));
            }
            return $data;
        }
        
        if ($max == null) {
            $max = $min;
        }
        
        $params = [];
        $params['dateMin'] = $min->format('Y-m-d\T00:00:00');
        $params['dateMax'] = $max->format('Y-m-d\T23:59:59');
        $params[$column] = $value;
        
        $SQL = "SELECT * FROM {$this->getTableName()} WHERE {$column} = :{$column} AND deliveryDate IS ".($delivered? 'NOT ':'')."NULL ORDER BY dateAjout DESC";
        $SQL .= " {$columnName} >= :dateMin AND {$columnName} <=:dateMax ";
        $SQL .= $limit !== null? " LIMIT {$limit} OFFSET {$offset}" : "";
        
        $return = [];
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL, $params);
            while ($row = $statement->fetch()) {
                $c = new Command($row);
                $c->setMember($this->memberDAOManager->findById($c->getMember()->getId()));
                $return[] = $c;
            }
            $statement->closeCursor();
            
            if (empty($return)) {
                throw new DAOException("no data matched by this selection query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::checkByMember()
     */
    public function checkByMember(int $memberId, ?bool $delivered = null, ?int $limit = null, int $offset = 0): bool {
        return $this->checkByForeignKey('member', $memberId, $delivered, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::countByMember()
     */
    public function countByMember(int $memberId, ?bool $delivered = null): int {
        return $this->countByForeignKey('member', $memberId, $delivered);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::findByMember()
     */
    public function findByMember (int $memberId, ?bool $delivered = null, ?int $limit = null, int $offset = 0) : array {
        return $this->findByForeignKey('member', $memberId, $delivered, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::checkByStatus()
     */
    public function checkByStatus(bool $delivered = false, ?int $limit = null, int $offset = 0): bool {
        $SQL = "SELECT * FROM {$this->getTableName()} WHERE deliveryDate IS ".($delivered? 'NOT ':'')."NULL";
        $SQL .= $limit !== null? " LIMIT {$limit} OFFSET {$offset}" : " LIMIT 1 OFFSET 0";
        
        $return = false;
        try {            
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL);
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
     * @see \Core\Shivalik\Managers\CommandDAOManager::countByStatus()
     */
    public function countByStatus(bool $delivered = false): int {
        $SQL = "SELECT COUNT(*) AS nombre FROM {$this->getTableName()} WHERE deliveryDate IS ".($delivered? 'NOT ':'')."NULL";
        $return = 0;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL);
            if ($row = $statement->fetch()) {
                $return = $row['nombre'];
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::findByStatus()
     */
    public function findByStatus(bool $delivered = false, ?int $limit = null, int $offset = 0): array {
        $SQL = "SELECT * FROM {$this->getTableName()} WHERE deliveryDate IS ".($delivered? 'NOT ':'')."NULL";
        $SQL .= $limit !== null? " LIMIT {$limit} OFFSET {$offset}" : " LIMIT 1 OFFSET 0";
        
        $return = [];
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $SQL);
            while ($row = $statement->fetch()) {
                $return[] = new Command($row);
            }
            $statement->closeCursor();
            if (empty($return)) {
                throw new DAOException("No data matched by this selection query");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update ($entity, $id): void {
        throw new DAOException("operation not supported");
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::checkByOffice()
     */
    public function checkByOffice(int $officeId, ?bool $delivered = null, ?int $limit = null, int $offset = 0): bool {
        return $this->checkByForeignKey('office', $officeId, $delivered, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::countByOffice()
     */
    public function countByOffice (int $officeId, ?bool $delivered = null): int {
        return $this->countByForeignKey('office', $officeId, $delivered);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::findByOffice()
     */
    public function findByOffice(int $officeId, ?bool $delivered = null, ?int $limit = null, int $offset = 0): array {
        return $this->findByForeignKey('office', $officeId, $delivered, $limit, $offset);
    }
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::checkByMemberAtDate()
     */
    public function checkByMemberAtDate(int $memberId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null, ?int $limit = null, int $offset = 0): bool {
        return $this->checkByForeignKeyAtDate('member', $memberId, $min, $max, $delivered, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::checkByOfficeAtDate()
     */
    public function checkByOfficeAtDate(int $officeId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null, ?int $limit = null, int $offset = 0): bool {
        return $this->checkByForeignKeyAtDate('office', $officeId, $min, $max, $delivered, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::countByMemberAtDate()
     */
    public function countByMemberAtDate(int $memberId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null): int {
        return $this->countByForeignKeyAtDate('member', $memberId, $min, $max, $delivered);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::countByOfficeAtDate()
     */
    public function countByOfficeAtDate(int $officeId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null): int {
        return $this->countByForeignKeyAtDate('office', $officeId, $min, $max, $delivered);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::findByMemberAtDate()
     */
    public function findByMemberAtDate(int $memberId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null, ?int $limit = null, int $offset = 0): array {
        return $this->findByForeignKeyAtDate('member', $memberId, $min, $max, $delivered, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\CommandDAOManager::findByOfficeAtDate()
     */
    public function findByOfficeAtDate(int $officeId, \DateTime $min, ?\DateTime $max = null, ?bool $delivered = null, ?int $limit = null, int $offset = 0): array {
        return $this->findByForeignKeyAtDate('office', $officeId, $min, $max, $delivered, $limit, $offset);
    }

}

