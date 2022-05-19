<?php
namespace Core\Shivalik\Entities;

use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 * Bonus de re-achat des produit
 */
class PurchaseBonus extends AbstractBonus
{
    /**
     * @var int|null
     */
    private $generation;
    
    /**
     * @var MonthlyOrder
     */
    private $monthlyOrder;
    
    /**
     * @return int|null
     */
    public function getGeneration ()
    {
        return $this->generation;
    }

    /**
     * @return \Core\Shivalik\Entities\MonthlyOrder
     */
    public function getMonthlyOrder() : ?MonthlyOrder
    {
        return $this->monthlyOrder;
    }

    /**
     * @param Ambigous <number, NULL> $generation
     */
    public function setGeneration($generation) : void
    {
        $this->generation = $generation;
    }

    /**
     * @param \Core\Shivalik\Entities\MonthlyOrder $monthlyOrder
     */
    public function setMonthlyOrder($monthlyOrder) : void
    {
        if ($monthlyOrder == null || $monthlyOrder instanceof MonthlyOrder) {
            $this->monthlyOrder = $monthlyOrder;
        } elseif(self::isInt($monthlyOrder)) {
            $this->monthlyOrder = new MonthlyOrder();
            $this->monthlyOrder->setId($monthlyOrder);
        } else {
            throw new PHPBackendException("Invalid argoument in setMonthlyOrder() : void method");
        }
    }

    
}

