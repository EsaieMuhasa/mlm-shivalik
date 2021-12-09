<?php
namespace Entities;

use Library\DBEntity;
use Library\LibException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class Operation extends DBEntity
{
    /**
     * @var Member
     */
    protected $member;
    
    /**
     * @var float
     */
    protected $amount;
    
    
    /**
     * @return \Entities\Member
     */
    public function getMember() : ?Member
    {
        return $this->member;
    }

    /**
     * @return number
     */
    public function getAmount() : ?float
    {
        return $this->amount;
    }

    /**
     * @param \Entities\Member $member
     */
    public function setMember($member) : void
    {
        if ($member == null || $member instanceof Member) {
            $this->member = $member;
        }elseif ($this->isInt($member)){
            $this->member = new Member(array('id' => $member));
        }else {
            throw new LibException("Invalid value in param of method setMember()");
        }
    }

    /**
     * @param number $amount
     */
    public function setAmount($amount) : void
    {
        $this->amount = $amount;
    }

}

