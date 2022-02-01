<?php

namespace PHPBackend;


use ArrayAccess;
use PHPBackend\Serialisation\JSONSerialize;
use PHPBackend\Serialisation\XMLSerialize;


/**
 *Classe de base pour toute les classe qui represente une entite de la base de donnees
 * @author Esaie Muhasa
 *        
 */
abstract class DBEntity implements ArrayAccess
{
    const RGX_INT='#^([0-9]+)$#';
    
    /**
     * Identifiant de l'entite
     * @var int
     */
    protected $id;
    
    /**
     * La date d'enregistrement de l'entite
     * @var \DateTime
     */
    protected $dateAjout;
    
    /**
     * La date de la  derniere modification apportee sur l'enregistrement
     * @var \DateTime
     */
    protected $dateModif;
    
    /**
     * Pour savoir si une occurence est deja mise en corbeil
     * @var boolean
     */
    protected $deleted;
    
    use JSONSerialize, XMLSerialize;

    /**
     * Constructeur d'initialisation
     * @param array $data
     * @param boolean $encripted specifie si les  donnees qui sont dans le tableaut sont crypter
     * le deuxieme parametre est utilise lors dela recuperation des donnees dans la base de donnees
     */
    public function __construct(?array $data = array())
    {
        $this->hydrate($data); 
        
        if ($this->getDateAjout() == null || empty($data)) {
            $this->setDateAjout(new \DateTime());
        }
    }
    
    /**
     * Pour hydrater un objet
     * @param array $data tableau associatif  des donnees
     * @param boolean $encrypted
     */
    public function hydrate (?array $data, $encrypted=false) : void {
        
        if ($data==null) {
            return;
        }
        $class = new \ReflectionClass($this);
        
        foreach ($data as $attr => $value) {
            $method = 'set'.ucfirst(trim($attr));
            if (is_callable(array($this, $method))) {
                $refMethode = $class->getMethod($method);
                if (count($refMethode->getParameters())==2 && $encrypted==true) {//Si les donnees sont cryptable
                    $this->$method($value, true);
                }else{
                    $this->$method($value);
                }
            }
        }

    }
    
    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDateAjout() : ?\DateTime
    {
        return $this->dateAjout;
    }
    
    /**
     * Renvoie la date d'ajout de l'occurence, formater
     * @param string $format
     * @return string|NULL
     */
    public function getFormatedDateAjout (string $format = \DateTime::W3C) : ?string {
        return ($this->getDateAjout() != null? $this->getDateAjout()->format($format) : null);
    }

    /**
     * @return \DateTime
     */
    public function getDateModif() : ?\DateTime
    {
        return $this->dateModif;
    }
    
    /**
     * renvoie la date du dernier modification formater
     * @param string $format
     * @return string|NULL
     */
    public function getFormatedDateModif (string $format = \DateTime::W3C) : ?string {
        return $this->getDateModif()!=null? $this->getDateModif()->format($format) : null;
    }

    /**
     * @param int $id
     */
    public function setId($id) : void
    {
        $this->id = intval($id, 10);
    }

    /**
     * @param \DateTime | string $dateAjout 
     */
    public function setDateAjout($dateAjout)
    {
        $this->dateAjout = $this->hydrateDate($dateAjout);
    }

    /**
     * @param \DateTime | string $dateModif
     */
    public function setDateModif($dateModif)
    {
        $this->dateModif = $this->hydrateDate($dateModif);
    }
    
    /**
     * Verfication d'une date
     * @param string | \DateTime $date
     * @return \DateTime|NULL
     */
    protected function hydrateDate( $date ) : ?\DateTime{
        if(is_string($date) || is_int($date))
            return new \DateTime($date);
        elseif ($date instanceof \DateTime) return $date;
        return null;
    }
    
    /***
     * Pour verifier si une valeur est un vrais entier
     * @param string | int $value
     * @return boolean
     */
    protected static function isInteger($value) : bool{
        return (is_int($value) || (is_string($value) && preg_match(self::RGX_INT, $value)));
    }
    
    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        $method='get'.ucfirst($offset);
        return isset($this->$offset) && is_callable(array($this, $method));
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        $getOffset = 'get'.ucfirst($offset);
        if (isset($this->$offset) && is_callable(array($this, $getOffset))) {
            return $this->$getOffset();
        }
        $ref = new \ReflectionClass($this);
        throw new PHPbackendException("la proprietée ".$offset.", n'est pas definie sur les objets de la classe \"".$ref->getName()."\"");
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        $method = 'set'.ucfirst($offset);
        if(isset($this->$offset) && is_array(array($this, $method))){
            $this->$method($value);
        } else{
            $ref = new \ReflectionClass($this);
            if (isset($this->$offset)){
                throw new PHPbackendException("la proprietée ".$offset.", des objets de la classe \"".$ref->getName().'" n\'est pas accessible en ecriture');
            } else{
                throw new PHPbackendException("la proprietée ".$offset.", n'est pas definie sur les objets de la classe ".$ref->getNamespaceName().'\\'.$ref->getName());
            }
        }
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        $ref = new \ReflectionClass($this);
        throw new PHPbackendException("Impossible de supprimer un propiete des objets de la classe \"".$ref->getName()."\"");
    }
    
    /**
     * verifier s'il est possible de metre en four une occurence.
     * cela et utile pour les occurence qui ne peuvent etre mise en jours avent depassement d'un delais precis
     * @return bool
     */
    public function canUpdate () : bool {
        return true;
    }
    
    /**
     * Pouvons-nous faire une supression definitive de cette occurence
     * @return bool
     */
    public function canDelete () : bool {
        return true;
    }
    
    /**
     * Pouvons-nous faire une supression temporaire de cette occurence
     * @return bool
     */
    public function canRemove () : bool {
        return true;
    }

    /**
     * Redefinition de la methode magique __get pour rendre le tout les attribut proteger et prive en lecture seul
     * @param string $name le nom de l'attribut a la quel on veut acceder
     * @throws PHPbackendException sera leve si vous vellez acceder a un attribut qui n'est pas definie dans l'objet
     * @return mixed le type de retour depand du type de l'attribut
     */
    public function __get($name){
        
        $refClass = new \ReflectionClass($this);
        
        /**
         * @var \ReflectionMethod[] $methods
         */
        $methods = $refClass->getMethods();
        
        $getName = 'get'.ucfirst($name);
        $isName = 'is'.ucfirst($name);
        foreach ($methods as $method) {
            if ($method->getName() == $getName && $method->isPublic()) {                
                return $this->$getName();
            }elseif ($method->getName() == $isName && $method->isPublic()){                
                return $this->$isName();
            }
        }
        
        $refProp = $refClass->getProperty($name);
        if ($refProp->isPublic()) {
            return $this->$name;
        }else if ($refProp->isPrivate() || $refProp->isProtected()){            
            throw new PHPbackendException('Cette attribut est inaccessibe en lecture');
        }
        else throw new PHPbackendException('Vous tantez d\'acceder a la propriete "'.$name.'" qui n\'est pas definie sur cette objet.');
    }
    
    /**
     * Verification si une propiete est accessible
     * @param string $name
     * @return bool
     */
    public function __isset(string $name) : bool{
        $refClass = new \ReflectionClass($this);
        
        /**
         * @var \ReflectionMethod[] $methods
         */
        $methods = $refClass->getMethods();
        
        $getName = 'get'.ucfirst($name);
        $isName = 'is'.ucfirst($name);
        foreach ($methods as $method) {
            if (($method->getName() == $getName && $method->isPublic()) || ($method->getName() == $isName && $method->isPublic())) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verification si une valeur est un entier
     * @param mixed $valeur
     * @return boolean
     */
    protected static function isInt($valeur){
        
        if($valeur===null || (is_string($valeur) && empty(trim($valeur)))){
            return false;
        }
        
        if(is_int($valeur) || (is_string($valeur) && preg_match(self::RGX_INT, $valeur))){
            return true;
        }
        
        return false;
    }
    
    /**
     * @param string|bool|int $bool
     * @return bool
     */
    protected static function isTrue ($bool) : bool {
    	if (is_bool($bool)) {
    		return $bool;
    	}
    	
    	if (!self::isInt($bool) && !is_string($bool)) {
    		return false;
    	}
    	
    	return ($bool == true || $bool >= 1 || $bool == '1' || $bool == 'true') ;
    }
    
    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted) : void
    {
        if (is_bool($deleted)) {
            $this->deleted = $deleted;
        }else if(is_numeric($deleted)){
            $this->deleted = ($deleted==1);
        }else{
            throw new PHPbackendException('Valeur invalide en paramtere de la methode setDeleted');
        }
    }
    
}
