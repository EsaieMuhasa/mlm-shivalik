<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *classe de base pour tout les utilisateurs
 * @author Esaie MHS
 *        
 */
abstract class User extends DBEntity implements Notifiable
{
    
    const KIND_M = 'M';
    const KIND_W = 'W';
    const KIND_M_TXT = 'Man';
    const KIND_W_TXT = 'Women';
    
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $postName;
    
    /**
     * @var string
     */
    protected $lastName;
    
    /**
     * @var string
     */
    protected $kind;
    
    /**
     * @var string
     */
    protected $pseudo;
    
    /**
     * @var string
     */
    protected $password;
    
    /**
     * @var string
     */
    protected $telephone;
    
    /**
     * @var string
     */
    protected $email;
    
    /**
     * @var string
     */
    protected $photo;
    
    /**
     * @var boolean
     */
    protected $enable;
    
    /**
     * @var Localisation
     */
    protected $localisation;
    
    
    /**
     * {@inheritDoc}
     * @see Notifiable::getData()
     */
    public function getData()
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see Notifiable::getKey()
     */
    public function getKey()
    {
        return $this->getId();
    }

    /**
     * {@inheritDoc}
     * @see Notifiable::getNickname()
     */
    public function getNickname(): string
    {
        return $this->getNames()!= null? $this->getName() : "";
    }

    /**
     * @return string
     */
    public function getName() :?string
    {
        return $this->name;
    }
    
    /**
     * return the fullname for user
     * @return string|NULL
     */
    public function getNames () : ?string{
        return "{$this->getName()} {$this->getPostName()} {$this->getLastName()}";
    }

    /**
     * @return string
     */
    public function getPostName() :?string
    {
        return $this->postName;
    }

    /**
     * @return string
     */
    public function getLastName() : ?string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getPseudo() : ?string
    {
        return $this->pseudo;
    }

    /**
     * @return string
     */
    public function getPassword() :?string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getTelephone() : ?string
    {
        return $this->telephone;
    }

    /**
     * @return string
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhoto() : ?string
    {
        return $this->photo;
    }
    
    public function getAbsolutPhoto () : ?string {
        if ($this->photo != null) {
            return $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'Web'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.$this->photo;
        }
        return $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'Web'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'user.png';
    }

    /**
     * @param string $name
     */
    public function setName($name) : void
    {
        $this->name = $name;
    }

    /**
     * @param string $postName
     */
    public function setPostName($postName) : void
    {
        $this->postName = $postName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName) : void
    {
        $this->lastName = $lastName;
    }

    /**
     * @param string $pseudo
     */
    public function setPseudo($pseudo) : void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) : void
    {
        $this->password = $password;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone($telephone) : void
    {
        $this->telephone = ($telephone!=null? "{$telephone}" : null);
    }

    /**
     * @param string $email
     */
    public function setEmail($email) : void
    {
        $this->email = $email;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo) : void
    {
        $this->photo = str_replace("\\", "/", $photo);
    }
    
    /**
     * @return boolean
     */
    public function isEnable() : ?bool
    {
        return $this->enable;
    }

    /**
     * @param boolean $enable
     */
    public function setEnable($enable) : void
    {
        $this->enable = ($enable == true || $enable == 1 || $enable == 'true');
    }
    
    /**
     * @return Localisation
     */
    public function getLocalisation() : ?Localisation
    {
        return $this->localisation;
    }

    /**
     * @param Localisation|int $localisation
     * @throws PHPBackendException
     */
    public function setLocalisation ($localisation) : void
    {
        if ($localisation == null || $localisation instanceof Localisation) {
            $this->localisation = $localisation;
        } elseif ($this->isInt($localisation)) {
            $this->localisation = new Localisation(array('id' => $localisation));
        } else {
            throw new PHPBackendException("invalid value in param of method setLocalisation");
        }
    }
    
    /**
     * @return string
     */
    public function getKind() : ?string
    {
        return $this->kind;
    }

    /**
     * @param string $kind
     */
    public function setKind($kind) : void 
    {
        $this->kind = $kind;
    }
    
    
 
}

