<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\PointValue;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\PointValueDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class PointValueDAOManagerImplementation1 extends AbstractBonusDAOManager implements PointValueDAOManager
{
    /**
     * @var MemberDAOManager
     */
    protected $memberDAOManager;
    
    /**
     * @var GradeMemberDAOManager
     */
    protected $gradeMemberDAOManager;
    
    
    /**
     * {@inheritDoc}
     */
    public function checkPv(int $memberId, ?int $foot = null, ?bool $product = null): bool
    {
        if ($foot === null && $product === null) {
            return $this->checkByMember($memberId);
        }
        
        $QUERY = "SELECT * FROM {$this->getTableName()} WHERE member = ?";
        $QUERY .= $foot !== null? " AND foot = {$foot}" : "";
        $QUERY .= $product !== null? (" AND command IS ".($product? 'NOT':''))." NULL" : "";
        $QUERY .= 'LIMIT 1';
        
        $return = false;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $QUERY, [$memberId]);
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
     */
    public function checkByGenerator(int $gradMember, ?int $limit = null, int $offset = 0): bool
    {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['generator' => $gradMember], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function findByGenerator(int $gradMember, ?int $limit = null, int $offset = 0): array{
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(),
            $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, ['generator' => $gradMember], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function update ($entity, $id) : void
    {
        throw new DAOException("no subsequent update of the point value is authorized");
    }
    
    /**
     * {@inheritDoc}
     * @param PointValue $pv
     */
    public function createInTransaction($pv, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'member' => $pv->getMember()->getId(),
            'generator' => $pv->getGenerator()->getId(),
            'value' => $pv->getValue(),
            'foot' => $pv->getFoot(),
            'monthlyOrder' => $pv->getMonthlyOrder() != null? $pv->getMonthlyOrder()->getId() : null,
            self::FIELD_DATE_AJOUT => $pv->getFormatedDateAjout()            
        ]);
        $pv->setId($id);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findPvByMember(int $memberId, ?int $foot = null, ?bool $product = null): array
    {
        if($foot == null &&  $product === null){
            return $this->findByMember($memberId);
        }
        
        $QUERY = "SELECT * FROM {$this->getTableName()} WHERE member = ?";
        $QUERY .= $foot !== null? " AND foot = {$foot}" : "";
        $QUERY .= $product !== null? (" AND command IS ".($product? 'NOT':''))." NULL" : "";
        
        $return = [];
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $QUERY, [$memberId]);
            while ($row = $statement->fetch()) {
                $return[] = new PointValue($row);
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        if (empty($return)) {
            throw new DAOException("No point value in database indexed to {$memberId} ID member account");
        }
        
        return $return;
    }
    
    /**
     * {@inheritDoc}
     */
    public function checkLeftPv (int $memberId, ?bool $product = null) : bool{
        return $this->checkPv($memberId, PointValue::FOOT_LEFT, $product);
    }
    
    /**
     * {@inheritDoc}
     */
    public function checkRightPv (int $memberId, ?bool $product = null) : bool{
        return $this->checkPv($memberId, PointValue::FOOT_RIGTH, $product);
    }
    
    /**
     * {@inheritDoc}
     */
    public function checkMiddlePv (int $memberId, ?bool $product = null) : bool{
        return $this->checkPv($memberId, PointValue::FOOT_MIDDEL, $product);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findLeftByMember (int $memberId, ?bool $product = null) : array{
        return $this->findPvByMember($memberId, PointValue::FOOT_LEFT, $product);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findRightByMember (int $memberId, ?bool $product = null) : array{
        return $this->findPvByMember($memberId, PointValue::FOOT_RIGTH, $product);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findMiddleByMember (int $memberId, ?bool $product = null) : array{
        return $this->findPvByMember($memberId, PointValue::FOOT_MIDDEL, $product);
    }
    /**
     * {@inheritDoc}
     */
    public function checkProductPvByMember(int $memberId): bool
    {
        $QUERY = "SELECT * FROM {$this->getTableName()} WHERE member = ? AND foot IS NULL AND command IS NOT NULL LIMIT 1";
        
        $return = false;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $QUERY, [$memberId]);
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
     */
    public function findProductPvByMember(int $memberId): array
    {
        $QUERY = "SELECT * FROM {$this->getTableName()} WHERE member = ? AND foot IS NULL AND command IS NOT NULL";
        
        $data = false;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $QUERY, [$memberId]);
            while ($row = $statement->fetch()) {
                $data[] = new PointValue($row);
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        if (empty($data)) {
            throw new DAOException("No point value in database indexed to {$memberId} ID member account");
        }
        
        return $data;
    }


}

