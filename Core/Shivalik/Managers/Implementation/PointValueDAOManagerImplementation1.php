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
     * @see \Core\Shivalik\Managers\PointValueDAOManager::checkPv()
     */
    public function checkPv(int $memberId, ?int $foot = null): bool
    {
        if ($foot === null) {
            return $this->checkByMember($memberId);
        }
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), [
            'member' => $memberId,
            'foot' => $foot
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AbstractOperationDAOManager::update()
     */
    public function update($entity, $id) : void
    {
        throw new DAOException("no subsequent update of the point value is authorized");
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param PointValue $pv
     */
    public function createInTransaction($pv, \PDO $pdo): void
    {
        $id = UtilitaireSQL::update($pdo, $this->getTableName(), [
            'member' => $pv->getMember()->getId(),
            'generator' => $pv->getGenerator()->getId(),
            'value' => $pv->getValue(),
            'foot' => $pv->getFoot(),
            self::FIELD_DATE_AJOUT => $pv->getFormatedDateAjout()            
        ]);
        $pv->setId($id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\PointValueDAOManager::findPvByMember()
     */
    public function findPvByMember(int $memberId, ?int $memberFoot = null): array
    {
        if($memberFoot == null ){
            return $this->findByMember($memberId);
        }
        
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, [
            'member' => $memberId,
            'foot' => $memberFoot
        ]);        
    }

}

