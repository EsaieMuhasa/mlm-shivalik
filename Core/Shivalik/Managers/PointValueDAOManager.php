<?php
namespace Core\Shivalik\Managers;


use Core\Shivalik\Entities\PointValue;

/**
 *
 * @author Esaie MHS
 *        
 */
interface PointValueDAOManager extends BonusDAOManager
{
    
    /**
     * memher check point value??
     * on foot
     * @param int $memberId
     * @param int $foot
     * @return bool
     */
    public function checkPv (int $memberId, ?int $foot = null) : bool;
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function checkLeftPv (int $memberId) : bool;
    
    /**
     *
     * @param int $memberId
     * @return bool
     */
    public function checkRightPv (int $memberId) : bool;
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function checkMiddlePv (int $memberId) : bool;

    /**
     * rvoie le PV sur l'un des pieds du membre
     * @param int $memberId
     * @param int $memberFoot
     * @return PointValue[]
     */
    public function findPvByMember (int $memberId, ?int $memberFoot = null) : array;
    
    /**
     * return all left point value of member
     * @param int $memberId
     * @return PointValue[]
     */
    public function findLeftByMember (int $memberId) : array;
    
    /**
     * 
     * @param int $memberId
     * @return PointValue[]
     */
    public function findRightByMember (int $memberId) : array;
    
    /**
     * @param int $memberId
     * @return PointValue[]
     */
    public function findMiddleByMember (int $memberId) : array;
    
    
}

