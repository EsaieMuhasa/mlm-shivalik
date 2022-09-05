<?php
namespace PHPBackend\Text;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AbstractFormater
{
    /**
     * Les donnees textuel a formater
     * @var string
     */
    protected $data;
    
    
    /**
     * le texte deja formater
     * @var string
     */
    protected $formated;

    /**
     */
    public function __construct(?string $data)
    {
        $this->data = $data;
    }
    
    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    
    /**
     * formatage du text au format bie precis
     * @return string|NULL
     */
    public abstract function format (): ?string;
}

