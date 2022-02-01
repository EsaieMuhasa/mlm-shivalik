<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class Commande extends DBEntity
{
    /**
     * date de livraison de la commande
     * @var \DateTime
     */
    private $deliveryDate;
    
    /**
     * @var int
     */
    private $pointValue;
    
    /**
     * @var Member
     */
    private $member;
    
    /**
     * @return \DateTime
     */
    public function getDeliveryDate() :?\DateTime
    {
        return $this->deliveryDate;
    }

    /**
     * @return number
     */
    public function getPointValue() : ?int
    {
        return $this->pointValue;
    }

    /**
     * @return \Core\Shivalik\Entities\Member
     */
    public function getMember() : ?Member
    {
        return $this->member;
    }

    /**
     * @param \DateTime $deliveryDate
     */
    public function setDeliveryDate($deliveryDate) : void
    {
        $this->deliveryDate = $this->hydrateDate($deliveryDate);
    }

    /**
     * @param number $pointValue
     */
    public function setPointValue($pointValue) : void
    {
        $this->pointValue = $pointValue;
    }

    /**
     * @param \Core\Shivalik\Entities\Member|int $member
     */
    public function setMember($member) : void
    {
        if ($member === null || $member instanceof Member) {
            $this->member = $member;
        } else if (self::isInt($member)) {
            $this->member = new Member(array('id' => $member));
        } else {
            throw new PHPBackendException("invalide argument value");
        }
    }
    
}

