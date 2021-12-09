<?php
namespace Library\Config;

/**
 * la configuration d'une connexion
 * @author Esaie MHS
 *        
 */
class DBConnection
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $user;
    
    /**
     * @var string
     */
    private $pasword;
    
    /**
     * @var string
     */
    private $dsn;
    
    /**
     * le max de connexion possible
     * @var int
     */
    private $max;

    /**
     * constructeur d'initialisation
     * @param string $name
     * @param string $dsn
     * @param string $user
     * @param string $password
     * @param int $max
     */
    public function __construct(string $name, string $dsn, string $user, ?string $password=null, ?int $max=null)
    {
        $this->name = $name;
        $this->user = $user;
        $this->pasword=$password;
        $this->dsn = $dsn;
        $this->max = $max;
    }
    
    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUser() : string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPasword() : ?string
    {
        return $this->pasword;
    }

    /**
     * @return string
     */
    public function getDsn() : string
    {
        return $this->dsn;
    }

    /**
     * @return number
     */
    public function getMax() : ?int
    {
        return $this->max;
    }

    
    
}

