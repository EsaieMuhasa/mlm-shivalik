<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Operation;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
interface OperationDAOManager extends DAOInterface
{    
    /**
     * verifie si dans le compte du membre il y a aumoin une operation
     * @param int $memberId
     * @return bool
     */
    public function checkByMember (int $memberId) :bool;
    
    /**
     * compte tout les operations d'un membre
     * @param int $memberId
     * @return int
     */
    public function countByMember (int $memberId) : int;
    
    /**
     * renvoie la collection des operations d'un membre
     * @param int $memberId
     * @param int $limit
     * @param int $offset
     * @return Operation[]
     */
    public function findByMember (int $memberId, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * verification de l'historique d'un compte d'un membre
     * @param int $memberId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function checkHistoryByMember (int $memberId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool;
    
    /**
     * Renvoie l'historique d'un compte d'un membre
     * @param int $memberId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return Operation[]
     */
    public function findHistoryByMember (int $memberId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array ;

}

