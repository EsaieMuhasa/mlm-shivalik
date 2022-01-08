<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AbstractOperationDAOManager extends DefaultDAOInterface
{
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id) : void
    {
        throw new DAOException("no subsequent update of the benefit is authorized");
    }
    
    /**
     * @param int $memberId
     * @return bool
     */
    public function checkByMember (int $memberId) :bool {
        return $this->columnValueExist('member', $memberId);
    }
    
    /**
     * compte tout les operations d'un membre
     * @param int $memberId
     * @return int
     */
    public function countByMember (int $memberId) : int {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), array("member" => $memberId));
    }
    
    /**
     * renvoie la collection des operations d'un membre
     * @param int $memberId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findByMember (int $memberId, ?int $limit = null, int $offset = 0) : array {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "dateAjout", true, array("member"=> $memberId), $limit, $offset);
    }

}

