<?php
namespace PHPBackend\Config;

use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class AppManagerConfig
{
    /**
     * les metadonnees des applications
     * @var AppMetadata[]
     */
    private $metadatas = [];
    
    /**
     * le dossicer racine
     * @var string
     */
    private $container;
    
    /**
     * @var AppManagerConfig
     */
    private static $instance;
    
    /**
     */
    private function __construct()
    {
        $xml = new \DOMDocument();
        $xmlFile = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'config.xml';
        
        /**
         * @var \DOMDocument $readFile
         */
        $readFile = $xml->load($xmlFile);
        if ($readFile === false) {
            throw new PHPBackendException('Impossible de parser le fichier de configuration globale "'.$this->getApplication()->getName().'" => ('.$xmlFile.')');
        }
        
        //l'aaplicatioon
        $applications = $xml->getElementsByTagName("applications");
        if ($applications->count() != 0) {
//             var_dump($applications->item(0)->attributes->item(0));
//             exit();
            $appFolder = $applications->item(0)->attributes->item(0)->nodeValue;
            $this->container = $appFolder;
            
            $elements = $applications[0]->childNodes;
            for ($i = 0; $i < $elements->length; $i++) {
                $element = $elements->item($i);
                
                if ($element->nodeName === 'application') {
                    $urlPattern = $element->hasAttribute('urlPattern')? $element->getAttribute('urlPattern') : null;
                    $appMetadata = new AppMetadata($element->getAttribute('name'), $urlPattern);
                    $this->metadatas[$element->getAttribute('name')] = $appMetadata;
                }
            }
        }
    }
    
    /**
     * recuperation d'une instance
     * @return AppManagerConfig
     */
    public static function getInstance () : AppManagerConfig {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * @return string
     */
    public function getContainer() : ?string
    {
        return $this->container;
    }

    /**
     * revoie l'application 
     * @param string $url
     * @return AppMetadata
     */
    public function findAppMetadata (string $url) : AppMetadata {
        foreach ($this->metadatas as $meta) {
            if (!$meta->isDefault() && $meta->match($url)) {
                return $meta;
            }
        }
        
        return $this->getDefaultApp();
    }
    
    /**
     * revoie l'application par defaut
     * @throws PHPBackendException
     * @return AppMetadata
     */
    public function getDefaultApp () : AppMetadata {
        if (empty($this->metadatas)) {
            throw new PHPBackendException("Aucune application dans le fichier de configuration");
        }
        
        foreach ($this->metadatas as $meta) {
            if ($meta->isDefault()) {
                return $meta;
            }
        }
        
        return $this->metadatas[array_key_first($this->metadatas)];
    }
}

