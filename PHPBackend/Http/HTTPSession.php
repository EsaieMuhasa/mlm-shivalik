<?php
namespace PHPBackend\Http;

use PHPBackend\Session;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class HTTPSession implements Session
{    
    /**
     * @var array
     */
    private $attributes = [];
    
    /**
     * determie si la session a ete decerialiser automatiquement par PHP
     * @var bool
     */
    private $auto;
    
    /**
     * l'identifiant de la session
     * @var string
     */
    private $id;
    
    
    /**
     * constructeur d'initialisation
     * @param HTTPApplication $application
     */
    public function __construct( ?string $data =  null, ?string $id=null) {
        
        if ($data != null) {
            $current = $_SESSION;
            
            session_decode($data);
            $this->attributes = $_SESSION;
            $_SESSION =  $current;
            $this->auto = false;
            $this->id = $id;
        } else {
            $this->auto = true;
            $this->id = session_id();
        }
    }
    
    /**
     * Renvoei la session actuel
     * @return HTTPSession
     */
    public static function getCurrent () : HTTPSession {
        return new self();
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Session::getName()
     */
    public function getName(): string
    {
        return session_name();
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Session::setName()
     */
    public function setName(string $name): void
    {
        session_name($name);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Session::addAttribute()
     */
    public function addAttribute(string $name, $value): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Session::addAttributes()
     */
    public function addAttributes(array $attribute): void
    {
        $_SESSION = array_merge($_SESSION, $attribute);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Session::getAttributes()
     */
    public function getAttributes(): array
    {
        return $this->auto? $_SESSION : $this->attributes;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Session::getId()
     */
    public function getId () : string
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Session::getStatus()
     */
    public function getStatus(): int
    {
        return session_status();
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Session::removeAttribute()
     */
    public function removeAttribute(string $name): void
    {
        if ($this->hasAttribute($name)) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Session::hasAttribute()
     */
    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->getAttributes());
    }
    
}

