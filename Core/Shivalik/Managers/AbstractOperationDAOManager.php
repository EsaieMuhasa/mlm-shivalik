<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

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
    
    /**
     * verification de l'historique d'un compte d'un membre
     * @param int $memberId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function checkHistoryByMember (int $memberId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool {
        return UtilitaireSQL::hasCreationHistory($this->getConnection(), $this->getTableName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['member' => $memberId], $limit, $offset);
    }
    
    /**
     * Revoie l'historique d'un compte d'un membre
     * @param int $memberId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findHistoryByMember (int $memberId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array {
        return UtilitaireSQL::findCreationHistory($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['member' => $memberId], $limit, $offset);
    }

}

