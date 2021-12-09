<?php
namespace Managers\Implementation;

use Managers\PointValueDAOManager;
use Library\DAOException;
use Managers\GradeMemberDAOManager;
use Managers\MemberDAOManager;
use Entities\PointValue;

/**
 *
 * @author Esaie MHS
 *        
 */
class PointValueDAOManagerImplementation1 extends PointValueDAOManager
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
     * @see \Library\AbstractDAOManager::create()
     */
    public function create($entity)
    {
        try {
            if ($this->pdo->beginTransaction()) {
                $this->createInTransaction($entity, $this->pdo);
                $this->pdo->commit();
            }else {
                throw new DAOException("An error occurred while creating the transaction");
            }
        } catch (\PDOException $e) {
            throw new DAOException("An error occurred in the plain banefice sharing transaction: {$e->getMessage()}", intval($e->getCode()), $e);
        }
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Managers\PointValueDAOManager::has()
     */
    public function has(int $memberId, ?int $foot = null): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE member={$memberId} ".($foot!=null? " AND foot={$foot}":""));
            if($statement->execute()){
                if ($statement->fetch()) {
                   $return = true;
                   $statement->closeCursor();
                } 
            }else {
                $statement->closeCursor();
                throw new DAOException("query execution failure");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), $e->getCode(), $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        throw new DAOException("no subsequent update of the point value is authorized");
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::createInTransaction()
     * @param PointValue $pv
     */
    public function createInTransaction($pv, $api): void
    {
        $id = $this->pdo_insertInTableTansactionnel($api, $this->getTableName(), array(
            'member' => $pv->getMember()->getId(),
            'generator' => $pv->getGenerator()->getId(),
            'value' => $pv->getValue(),
            'foot' => $pv->getFoot()
        ));
        $pv->setId($id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Managers\PointValueDAOManager::ofMember()
     */
    public function ofMember(int $memberId, ?int $memberFoot = null): array
    {
        if($memberFoot == null ){
            return $this->forMember($memberId);
        }
        
        $pvs = array();
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE member=:member AND foot=:foot");
            if($statement->execute(array('member' => $memberId, 'foot' => $memberFoot))){
                if($row = $statement->fetch()){
                    $pvs[] = new PointValue($row);
                    while ($row = $statement->fetch()) {
                        $pvs[] = new PointValue($row);
                    }
                } else {
                    $statement->closeCursor();
                    throw new DAOException("no PV on the foot {$memberFoot}");
                }
            } else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $pvs;
        
    }

}

