<?php
namespace PHPBackend\Http;

use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
class HTTPCookie
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var mixed
     */
    private $value=null;
    
    /**
     * @var string
     */
    private $domain=null;
    
    /**
     * @var integer
     */
    private $expiration=0;
    
    /**
     * @var string
     */
    private $path=null;
    
    /**
     * @var boolean
     */
    private $secure=true;
    
    /**
     * @var boolean
     */
    private $httpOnly=false;

    /**
     * Constriction d'un cookie
     * @param PHPBackendException $application
     * @param string $name
     * @param mixed $value
     * @param number $expiration
     */
    public function __construct(HTTPApplication $application, string $name, $value=null, $expiration=0)
    {
        parent::__construct($application);
        $this->name = $this->setName($name);
        $this->value =$value;
        $this->expiration = $expiration;
    }
    
    /**
     * Construction d'un cookie dont le key dans le $_COOKIE et $name
     * @param HTTPApplication $application
     * @param string $name la cle de la cookie dans $_COOKIE
     * @return \PHPBackend\Http\HTTPCookie|NULL
     */
    public static function buildCookie(HTTPApplication $application, $name)
    {
        if (isset($_COOKIE[$application->getName().'__'.$name])) {
            $cookie = new HTTPCookie($application, $name);
            $cookie->setValue($_COOKIE[$cookie->getName()]['value']);
            $cookie->setDomain($_COOKIE[$cookie->getName()]['domain']);
            $cookie->setExpiration($_COOKIE[$cookie->getName()]['expires']);
            $cookie->setPath($_COOKIE[$cookie->getName()]['path']);
            $cookie->setSecure($_COOKIE[$cookie->getName()]['secure']);
            $cookie->setHttpOnly($_COOKIE[$cookie->getName()]['httpOnly']);
            return $cookie;
        }
        return null;
    }
    
    /**
     * Construction d'une cookie a partie d'un tableau
     * @param HTTPApplication $application
     * @param string $name
     * @param array $data
     * @throws PHPBackendException::
     * @return \PHPBackend\Http\HTTPCookie
     */
    public static function buildForData(HTTPApplication $application, string $name, array $data)
    {
        if (isset($data['value']) && isset($data['domain']) && isset($data['expires']) 
            && isset($data['path']) && isset($data['secure']) && isset($data['httpOnly'])) {
            $cookie = new HTTPCookie($application, $name);
            $cookie->setValue($data['value']);
            $cookie->setDomain($data['domain']);
            $cookie->setExpiration($data['expires']);
            $cookie->setPath($data['path']);
            $cookie->setSecure($data['secure']);
            $cookie->setHttpOnly($data['httpOnly']);
            return $cookie;
        }
        throw new PHPBackendException('Les donnees de costruction du cookie doivent etre valide', 500);
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return number
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @return boolean
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * @param string $name
     */
    protected function setName($name)
    {
        $this->name = $this->getApplication()->getName().'__'.$name;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param number $expiration
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param boolean $secure
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @param boolean $httpOnly
     */
    public function setHttpOnly($httpOnly)
    {
        $this->httpOnly = $httpOnly;
    }

}

