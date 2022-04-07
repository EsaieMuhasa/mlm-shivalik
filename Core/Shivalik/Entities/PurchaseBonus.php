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
     * @var Command
     */
    private $command;
    
    /**
     * @return int|null
     */
    public function getGeneration ()
    {
        return $this->generation;
    }

    /**
     * @return \Core\Shivalik\Entities\Command
     */
    public function getCommand() : ?Command
    {
        return $this->command;
    }

    /**
     * @param Ambigous <number, NULL> $generation
     */
    public function setGeneration($generation) : void
    {
        $this->generation = $generation;
    }

    /**
     * @param \Core\Shivalik\Entities\Command $command
     */
    public function setCommand($command) : void
    {
        if ($command == null || $command instanceof Command) {
            $this->command = $command;
        } elseif(self::isInt($command)) {
            $this->command = new Command(['id' => $command]);
        } else {
            throw new PHPBackendException("Invalid argoument in setCommand() : void method");
        }
    }

    
}

