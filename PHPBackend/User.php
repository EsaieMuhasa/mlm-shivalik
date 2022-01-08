<?php
namespace PHPBackend;

/**
 *
 * @author Esaie MHS
 *        
 */
class User
{
    
    /**
     * @var string
     */
    private $pseudo;
    
    /**
     * @var string
     */
    private $password;
    
    /**
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $pseudo
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

}

