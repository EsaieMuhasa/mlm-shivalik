<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class Command extends DBEntity
{
    /**
     * date de livraison de la commande
     * @var \DateTime
     */
    private $deliveryDate;
    
    /**
     * dans le cas ou c'est un membre adherant qui ait effectuer la commande, on garde une reference
     * @var Member
     */
    private $member;
    
    /**
     * l'office dans la quel la commande a ete faite
     * @var Office
     */
    private $office;
    
    /**
     * l'admin qui aurait valider la livraison/l'enregistrement de la commande 
     * @var OfficeAdmin
     */
    private $officeAdmin;
    
    /**
     * une note, si necessaire
     * @var string
     */
    private $note;
    
    /**
     * @return \DateTime
     */
    public function getDeliveryDate() :?\DateTime
    {
        return $this->deliveryDate;
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
    
    /**
     * @return \Core\Shivalik\Entities\Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @return \Core\Shivalik\Entities\OfficeAdmin
     */
    public function getOfficeAdmin() : ?OfficeAdmin
    {
        return $this->officeAdmin;
    }

    /**
     * @return string
     */
    public function getNote() : ?string
    {
        return $this->note;
    }

    /**
     * @param \Core\Shivalik\Entities\Office $office
     */
    public function setOffice($office) : void
    {
        if($office == null || $office instanceof Office) {
            $this->office = $office;
        } else if (self::isInt($office)) {
            $this->office = new Office(['id' => $office]);
        } else {
            throw new PHPBackendException("Invalid argument in setOffice(): void method");
        }
    }

    /**
     * @param \Core\Shivalik\Entities\OfficeAdmin $officeAdmin
     */
    public function setOfficeAdmin($officeAdmin) : void
    {
        if($officeAdmin == null || $officeAdmin instanceof OfficeAdmin) {
            $this->officeAdmin = $officeAdmin;
        } else if (self::isInt($officeAdmin)) {
            $this->officeAdmin = new OfficeAdmin(['id' => $officeAdmin]);
        } else {
            throw new PHPBackendException("invalide argument value in setOfficeAdmin() : void method");
        }
            
    }

    /**
     * @param string $note
     */
    public function setNote($note) : void
    {
        $this->note = $note;
    }

    
}

