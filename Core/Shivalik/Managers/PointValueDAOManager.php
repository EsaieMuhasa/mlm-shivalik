<?php
namespace Core\Shivalik\Managers;


use Core\Shivalik\Entities\PointValue;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class PointValueDAOManager extends AbstractBonusDAOManager
{
    
    /**
     * memher check point value??
     * on foot
     * @param int $memberId
     * @param int $foot
     * @return bool
     */
    public abstract function checkPv (int $memberId, ?int $foot = null) : bool;
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function checkLeftPv (int $memberId) : bool{
        return $this->checkPv($memberId, PointValue::FOOT_LEFT);
    }
    
    /**
     *
     * @param int $memberId
     * @return bool
     */
    public function checkRightPv (int $memberId) : bool{
        return $this->checkPv($memberId, PointValue::FOOT_RIGTH);
    }
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function checkMiddlePv (int $memberId) : bool{
        return $this->checkPv($memberId, PointValue::FOOT_MIDDEL);
    }

    /**
     * rvoie le PV sur l'un des pieds du membre
     * @param int $memberId
     * @param int $memberFoot
     * @return PointValue[]
     */
    public abstract function findPvByMember (int $memberId, ?int $memberFoot = null) : array;
    
    /**
     * return all left point value of member
     * @param int $memberId
     * @return PointValue[]
     */
    public function findLeftByMember (int $memberId) : array{
        return $this->findPvByMember($memberId, PointValue::FOOT_LEFT);
    }
    
    /**
     * 
     * @param int $memberId
     * @return PointValue[]
     */
    public function findRightByMember (int $memberId) : array{
        return $this->findPvByMember($memberId, PointValue::FOOT_RIGTH);
    }
    
    /**
     * @param int $memberId
     * @return PointValue[]
     */
    public function findMiddleByMember (int $memberId) : array{
        return $this->findPvByMember($memberId, PointValue::FOOT_MIDDEL);
    }
    
    
}

