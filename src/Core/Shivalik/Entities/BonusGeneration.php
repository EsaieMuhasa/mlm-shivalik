<?php
namespace Core\Shivalik\Entities;


use PHPBackend\PHPBackendException;

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
     * @return Generation
     */
    public function getGeneration() : ?Generation
    {
        return $this->generation;
    }

    /**
     * @param Generation|int $generation
     * @throws PHPBackendException
     */
    public function setGeneration($generation) : void
    {
        if ($generation == null || $generation instanceof Generation) {
            $this->generation = $generation;
        }elseif ($this->isInt($generation)){
            $this->old = new Generation(array('id' => $generation));
        }else {
            throw new PHPBackendException("Invalid value in param of method setGradeMember()");
        }
    }

}

