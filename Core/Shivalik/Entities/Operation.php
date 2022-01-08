<?php
namespace Core\Shivalik\Entities;;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

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
     * @return Member
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
     * @param Member $member
     */
    public function setMember($member) : void
    {
        if ($member == null || $member instanceof Member) {
            $this->member = $member;
        }elseif ($this->isInt($member)){
            $this->member = new Member(array('id' => $member));
        }else {
            throw new PHPBackendException("Invalid value in param of method setMember()");
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

