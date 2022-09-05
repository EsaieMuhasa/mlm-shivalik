<?php
namespace PHPBackend\Config;

use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class GlobalConfig
{
    
    /**
     * collection des constage
     * @var VarDefine[]
     */
    private $definitions=array();
    
    /**
     * le dossier publique de l'application
     * @var string
     */
    private $publicDirectory;
    
    /**
     * le dossier logg de l'application
     * @var string
     */
    private $loggDirectory;
   
    
    /**
     * @var GlobalConfig
     */
    private static $instance;

    /**
     */
    private function __construct()
    {}
    
    /**
     * revoie une insate ce du lib config
     * @return GlobalConfig
     */
    public static function getInstance () : GlobalConfig {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    
    /**
     * Revoie le chemain vers le dossier publique
     * @return string
     */
    public function getPublicDirectory() : string
    {
        if ($this->publicDirectory == null) {
            $this->loadConfig();
        }
        return $this->publicDirectory;
    }

    /**
     * Revoie le chemain vers le dossier de journalisation des erreurs
     * @return string
     */
    public function getLoggDirectory() : string
    {
        if ($this->loggDirectory == null) {
            $this->loadConfig();
        }
        return $this->loggDirectory;
    }

    /**
     * Recuperation d'un parametre dans le fichier de configuration generale
     * @param string $name le nom du parammetre
     * @throws PHPBackendException
     * @return NULL|VarDefine|VarList la valeur du parametre
     */
    public function get ($name)
    {
        if (empty($this->definitions)) {
            $this->loadConfig();
        }
        return isset($this->definitions[$name])? $this->definitions[$name] : null;
    }
    
    /**
     * chargement du fichier de configuration globale
     * @throws PHPBackendException
     */
    protected function loadConfig () : void {
        $this->definitions = [];
        $xml = new \DOMDocument();
        $xmlFile = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'config.xml';
        
        /**
         * @var \DOMDocument $readFile
         */
        $readFile = $xml->load($xmlFile);
        if ($readFile === false) {
            throw new PHPBackendException('Impossible de parser le fichier de configuration globale "'.$this->getApplication()->getName().'" => ('.$xmlFile.')');
        }

        
        $root = $xml->getElementsByTagName("config")->item(0);
        $this->publicDirectory = $root->getAttribute('webData');
        $this->loggDirectory = $root->getAttribute('webLogger');
        
        
        //var_dump('var_dump', $root, $this->publicDirectory, $this->loggDirectory);exit();
        
        $defines = $xml->getElementsByTagName('definitions');
        if ($defines->count() != 0) {
            $elements = $defines[0]->childNodes;
            for ($i = 0; $i < $elements->length; $i++) {
                $element = $elements->item($i);
                if ($element->nodeName === 'list') {
                    $list = $this->readList($element);
                    $this->definitions[$list->getName()] = $list;
                }else if ($element->nodeName === 'define') {
                    $label = $element->hasAttribute('label')? $element->getAttribute('label') : null;
                    $var = new VarDefine($element->getAttribute('name'), $element->getAttribute('value'), $label);
                    $this->definitions[$element->getAttribute('name')] = $var;
                }
            }
        }
        
    }

    
    /**
     * chargement des contenues d'une liste
     * @param \DOMElement $element
     * @return VarList
     */
    protected function readList(\DOMElement $element) : VarList {
        $items = array();
        $name = $element->getAttribute('name');
        $label = $element->hasAttribute('label')? $element->getAttribute('label') : null;
        
        $childrens = $element->childNodes;
        for ($i = 0; $i < $childrens->length; $i++) {
            $child = $childrens->item($i);
            if ($child->nodeName === 'item') {
                $item = null;
                if ($child->hasChildNodes()) {//pour une liste dans un element d'une liste
                    for ($j = 0; $j < $child->childNodes->length; $j++) {
                        if ($child->childNodes->item($j)->nodeName === 'list') {
                            $item = new VarDefine($child->hasAttribute('name')? $child->getAttribute('name') : '', $this->readList($child->childNodes->item($j)));
                            break;
                        }
                    }
                }else{
                    $item = new VarDefine($child->hasAttribute('name')? $child->getAttribute('name') : '', $child->getAttribute('value'), $child->hasAttribute('label')? $child->getAttribute('label') : null);
                }
                
                if($child->hasAttribute('name')){
                    $items[$child->getAttribute('name')] = $item;
                }else{
                    $items[] = $item;
                }
            }
        }
        
        return new VarList($name, $items, $label);
    }

}

