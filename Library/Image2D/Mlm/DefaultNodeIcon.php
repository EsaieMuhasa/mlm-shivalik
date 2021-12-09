<?php
namespace Library\Image2D\Mlm;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class DefaultNodeIcon implements NodeIcon
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $extension;
    
    /**
     * @var string
     */
    private $fullName;

    /**
     * constructeur d'initialisation
     * @param string $fullName
     */
    public function __construct(string $fullName)
    {
        $matches = array();
        if (preg_match('#^(.+)(\.[a-zA-Z]{3,4})$#', $fullName, $matches)) {
            $this->name = $matches[1];
            $this->extension = $matches[2];
        }
        $this->fullName = $fullName;
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\NodeIcon::getType()
     */
    public function getType(): string
    {
        return $this->extension;
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\NodeIcon::getDefault()
     */
    public function getDefault(): string
    {
        return $this->fullName;
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\NodeIcon::getMd()
     */
    public function getMd(): string
    {
        return "{$this->name}-md{$this->extension}";
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\NodeIcon::getSm()
     */
    public function getSm(): string
    {
        return "{$this->name}-sm{$this->extension}";
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\NodeIcon::getXs()
     */
    public function getXs(): string
    {
        return "{$this->name}-xs{$this->extension}";
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\NodeIcon::getAbsoluteDefault()
     */
    public function getAbsoluteDefault(): string
    {
        return "{$_SERVER["DOCUMENT_ROOT"]}/Web/data/{$this->name}{$this->extension}";
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\NodeIcon::getAbsoluteMd()
     */
    public function getAbsoluteMd(): string
    {
        return "{$_SERVER["DOCUMENT_ROOT"]}/Web/data/{$this->name}-md{$this->extension}";
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\NodeIcon::getAbsoluteSm()
     */
    public function getAbsoluteSm(): string
    {
        return "{$_SERVER["DOCUMENT_ROOT"]}/Web/data/{$this->name}-sm{$this->extension}";
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\NodeIcon::getAbsoluteXs()
     */
    public function getAbsoluteXs(): string
    {
        return "{$_SERVER["DOCUMENT_ROOT"]}/Web/data/{$this->name}-xs{$this->extension}";
    }


}

