<?php
namespace PHPBackend\Config;

use FontLib\Table\Type\name;

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
     * le namespace de base des elements de cette application
     *
     * @var string
     */
    private $namespace;
    
    /**
     * le sous domain de l'application
     * @var string
     */
    private $urlPattern;

    /**
     * constructreur d'initialisation
     * @param string $name
     * @param string $namespace
     * @param string $urlPattern
     */
    public function __construct(string $name, string $namespace, ?string $urlPattern)
    {
        $this->name = $name;
        $this->namespace = $namespace;
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


    /**
     * Get le namespace de base des elements de cette application
     * @return  string
     */ 
    public function getNamespace() : string
    {
        return $this->namespace;
    }
}

