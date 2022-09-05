<?php
namespace Core\Shivalik\Entities;

use PHPBackend\PHPBackendException;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class Command extends Operation
{
    /**
     * date de livraison de la commande
     * @var \DateTime
     */
    private $deliveryDate;
    
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
     * @var MonthlyOrder
     */
    private $monthlyOrder;
    
    /**
     * Rubrique de la commande
     * @var ProductOrdered[]
     */
    private $products = [];
    
    /**
     * @return \DateTime
     */
    public function getDeliveryDate() :?\DateTime {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime $deliveryDate
     */
    public function setDeliveryDate($deliveryDate) : void {
        $this->deliveryDate = $this->hydrateDate($deliveryDate);
    }
    
    /**
     * @return \Core\Shivalik\Entities\Office
     */
    public function getOffice() : ?Office {
        return $this->office;
    }

    /**
     * @return \Core\Shivalik\Entities\OfficeAdmin
     */
    public function getOfficeAdmin() : ?OfficeAdmin {
        return $this->officeAdmin;
    }

    /**
     * @return string
     */
    public function getNote() : ?string {
        return $this->note;
    }

    /**
     * @param \Core\Shivalik\Entities\Office $office
     */
    public function setOffice($office) : void {
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
    public function setOfficeAdmin($officeAdmin) : void {
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
    public function setNote($note) : void {
        $this->note = $note;
    }
    
    /**
     * @return \Core\Shivalik\Entities\ProductOrdered []
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * @param \Core\Shivalik\Entities\ProductOrdered[]  $products
     */
    public function setProducts(array $products) : void {
        foreach ($products as $product) {
            $product->setCommand($this);
        }
        $this->products = $products;
    }
    
    /**
     * comptage du nombre de produit sur la commande
     * @return int
     */
    public function getCountProduct() : int  {
        if ($this->products != null && !empty($this->products)) {
            return count($this->products);
        }
        return 0;
    }
    
    /**
     * renvoie le montant total a payer pour la commande
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Operation::getAmount()
     */
    public function getAmount () : ?float {
        if($this->getCountProduct() != 0) {
            $amount = 0;
            foreach ($this->products as $pr) {
                $amount += $pr->getAmount();
            }
            return $amount;
        }
        return 0;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Operation::setAmount()
     */
    public function setAmount($amount): void {
        throw new DAOException("Opertion not supported");
    }

    /**
     * Renvoie la sommes des prix unitaire
     * @return float
     */
    public function getTotalUnitPrice () : float {
        if($this->getCountProduct() != 0) {
            $amount = 0;
            foreach ($this->products as $pr) {
                $amount += $pr->getStock()->getUnitPrice();
            }
            return $amount;
        }
        return 0;
    }
    
    /**
     * Renvoie la quantite total des elements sur la commande
     * @return int
     */
    public function getTotalQuantity () : int  {
        if($this->getCountProduct() != 0) {
            $qt = 0;
            foreach ($this->products as $pr) {
                $qt += $pr->getQuantity();
            }
            return $qt;
        }
        return 0;
    }
    
    /**
     * @return \Core\Shivalik\Entities\MonthlyOrder
     */
    public function getMonthlyOrder () : ?MonthlyOrder {
        return $this->monthlyOrder;
    }

    /**
     * @param \Core\Shivalik\Entities\MonthlyOrder|int $monthlyOrder
     */
    public function setMonthlyOrder ($monthlyOrder) {
        if ($monthlyOrder == null || $monthlyOrder instanceof MonthlyOrder) {
            $this->monthlyOrder = $monthlyOrder;
        } else if (self::isInt($monthlyOrder)) {
            $this->monthlyOrder = new MonthlyOrder(['monthlyOrder' => $monthlyOrder]);
        } else {
            throw new DAOException("invalid argument as method parameter setMonthOrder()");
        }
    }

}

