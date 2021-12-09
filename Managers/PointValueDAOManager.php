<?php
namespace Managers;

use Entities\PointValue;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class PointValueDAOManager extends AbstractBonusDAOManager
{
    
    /**
     * memher has point value??
     * on foot
     * @param int $memberId
     * @param int $foot
     * @return bool
     */
    public abstract function has (int $memberId, ?int $foot = null) : bool;
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function hasLeft (int $memberId) : bool{
        return $this->has($memberId, PointValue::FOOT_LEFT);
    }
    
    /**
     *
     * @param int $memberId
     * @return bool
     */
    public function hasRight (int $memberId) : bool{
        return $this->has($memberId, PointValue::FOOT_RIGTH);
    }
    
    /***
     * 
     * @param int $memberId
     * @return bool
     */
    public function hasMiddle (int $memberId) : bool{
        return $this->has($memberId, PointValue::FOOT_MIDDEL);
    }

    /**
     * rvoie le PV sur l'un des pieds du membre
     * @param int $memberId
     * @param int $memberFoot
     * @return PointValue[]
     */
    public abstract function ofMember (int $memberId, ?int $memberFoot = null) : array;
    
    /**
     * return all left point value of member
     * @param int $memberId
     * @return PointValue[]
     */
    public function leftOfMember (int $memberId) : array{
        return $this->ofMember($memberId, PointValue::FOOT_LEFT);
    }
    
    /**
     * 
     * @param int $memberId
     * @return PointValue[]
     */
    public function rightOfMember (int $memberId) : array{
        return $this->ofMember($memberId, PointValue::FOOT_RIGTH);
    }
    
    /**
     * @param int $memberId
     * @return PointValue[]
     */
    public function middleOfMember (int $memberId) : array{
        return $this->ofMember($memberId, PointValue::FOOT_MIDDEL);
    }
    
    
}

