<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class Notification extends DBEntity
{
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var string
     */
    private $description;
    
    /**
     * @return string
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title) : void
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

}

