<?php
namespace Core\Shivalik\Entities;

/**
 *
 * @author Esaie MHS
 *        
 */
class PointValue extends AbstractBonus
{
    const FOOT_LEFT = 1;
    const FOOT_MIDDEL = 2;
    const FOOT_RIGTH = 3;
    
    
    /**
     * @var int
     */
    private $foot;

    /**
     * @return int
     */
    public function getFoot() : ?int
    {
        return $this->foot;
    }

    /**
     * @param int $foot
     */
    public function setFoot($foot) : void 
    {
        if ($foot < self::FOOT_LEFT) {
            $foot = self::FOOT_LEFT;
        }
        
        if ($foot > self::FOOT_RIGTH) {
            $foot = self::FOOT_RIGTH;
        }
        
        $this->foot = $foot;
    }
    
    /**
     * @return string
     */
    public function getFootName () : ?string {
        switch ($this->getFoot()) {
            case self::FOOT_LEFT:{
                return "LEFT";
            }break;
            case self::FOOT_MIDDEL:{
                return "MIDDLE";
            }break;
            case self::FOOT_RIGTH:{
                return "RIGTH";
            }break;
        }
        return null;
    }

    /**
     * lien de la methode @method getAmount() 
     * @return float|NULL
     */
    public function getValue () : ?float{
        return $this->getAmount();
    }
    
    /**
     * lien de la methode @method setAmount()
     * @param float $value
     */
    public function setValue ($value) : void {
        $this->setAmount($value);
    }
    
}

