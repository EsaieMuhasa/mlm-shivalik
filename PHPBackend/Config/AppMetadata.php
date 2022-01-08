<?php
namespace PHPBackend\Config;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class AppMetadata
{
    
    /**
     * le nom de l'application
     * doit etre un dossier qui existe physiquement dur le serveur
     * @var string
     */
    private $name;
    
    /**
     * le sous domain de l'application
     * @var string
     */
    private $urlPattern;

    /**
     * constructreur d'initialisation
     * @param string $name
     * @param string $urlPattern
     */
    public function __construct(string $name, ?string $urlPattern)
    {
        $this->name = $name;
        $this->urlPattern = $urlPattern;
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
    public function getUrlPattern() : ?string
    {
        return $this->urlPattern;
    }
    
    /**
     * @param string $url
     */
    public function match (?string $url) : bool {
        if (preg_match("#^{$this->getUrlPattern()}$#", $url)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Est-ce l'application par defaut??
     * @return bool
     */
    public function isDefault () : bool {
        return $this->urlPattern === null;
    }

}

