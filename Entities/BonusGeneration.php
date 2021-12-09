<?php
namespace Entities;

use Library\LibException;

/**
 *
 * @author Esaie MHS
 *        
 */
class BonusGeneration extends AbstractBonus
{    
    /**
     * une reference vers la generation actuel du beneficier
     * @var Generation
     */
    private $generation;

    /**
     * @return \Entities\Generation
     */
    public function getGeneration() : ?Generation
    {
        return $this->generation;
    }

    /**
     * @param \Entities\Generation $generation
     */
    public function setGeneration($generation) : void
    {
        if ($generation == null || $generation instanceof Generation) {
            $this->generation = $generation;
        }elseif ($this->isInt($generation)){
            $this->old = new Generation(array('id' => $generation));
        }else {
            throw new LibException("Invalid value in param of method setGradeMember()");
        }
    }

}

