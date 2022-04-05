<?php
namespace Core\Shivalik\Entities;


use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AbstractBonus extends Operation
{
    
    /**
     * on garde la reference vers l'actuel generator du membre
     * dont pour la classe d'association pour le membre et le generator
     * @var GradeMember
     */
    protected $generator;
    
    
    /**
     * @return GradeMember
     */
    public function getGenerator() : ?GradeMember
    {
        return $this->generator;
    }
    
    /**
     * @param GradeMember|int $generator
     * @throws PHPBackendException
     */
    public function setGenerator($generator) : void
    {
        if ($generator == null || $generator instanceof GradeMember) {
            $this->generator = $generator;
        }elseif ($this->isInt($generator)){
            $this->generator = new GradeMember(array('id' => $generator));
        }else {
            throw new PHPBackendException("Invalid value in param of method setGenerator()");
        }
    }
}

