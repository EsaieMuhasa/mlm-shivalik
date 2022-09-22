<?php
namespace PHPBackend;

use DOMElement;
use PHPBackend\Config\AppManagerConfig;
use PHPBackend\Config\VarList;
use PHPBackend\Config\VarDefine;
use PHPBackend\Config\GlobalConfig;

/**
 *
 * @author Esaie MHS
 * 
 */
class AppConfig
{
    /**
     * collection des constage
     * @var VarDefine[]
     */
    private $definitions=array();
    
    /**
     * @var User[]
     */
    private $users = array();
    
    /**
     * @var string
     */
    private $appName;
    
    /**
     * reference vers la derniere instance de la dite classe
     * @var AppConfig[]
     */
    private static $instances = [];
    
    /**
     * constructeur d'initialisation
     * @param string $appName
     */
    private function __construct(string $appName)
    {
        $this->appName = $appName;
    }

    /**
     * @return \PHPBackend\AppConfig
     */
    public static function getInstance (string $appName) : ?AppConfig
    {
        if (!array_key_exists($appName, self::$instances)) {
            self::$instances[$appName] = new self($appName);            
        }
        return self::$instances[$appName];
    }

    /**
     * Recuperation d'un parametre dans le fichier de configuration
     * @param string $name le nom du parammetre
     * @throws PHPBackendException
     * @return NULL|VarDefine|VarList la valeur du parametre
     */
    public function get($name)
    {
        if (empty($this->definitions)) {
            
            $xml = new \DOMDocument();
            
            //Lecture des parametre specifique a l'application
            //================================================
            $appConfig = AppManagerConfig::getInstance();
            $xmlFile = dirname(__DIR__, 2).DIRECTORY_SEPARATOR."{$appConfig->getContainer()}/{$this->appName}/Config/app-config.xml";
            $readFile = $xml->load($xmlFile);
            
            if ($readFile===false) {
                throw new PHPBackendException('Impossible de parser le fichier de configuration de l\'application "'.$this->appName.'" => ('.$xmlFile.')');
            }
            
            $defines = $xml->getElementsByTagName('definitions');
            
            if ($defines->count() != 0) {
                $elements = $defines[0]->childNodes;
                for ($i = 0; $i < $elements->length; $i++) {
                    $element = $elements->item($i);
                    if ($element->nodeName === 'list') {
                        $list = $this->readList($element);
                        $this->definitions[$list->getName()] = $list;
                    }else if ($element->nodeName === 'define') {
                        $var = new VarDefine($element->getAttribute('name'), $element->getAttribute('value'));
                        $this->definitions[$element->getAttribute('name')] = $var;
                    }
                }
            } 
        }
        
        return isset($this->definitions[$name])? $this->definitions[$name] : GlobalConfig::getInstance()->get($name);
    }
    
    
    /**
     * Recuperation d'un utilisateur
     * @param string $pseudo
     * @return User|null
     */
    public function getUser($pseudo)
    {
        if (empty($this->users)) {
            $xml = new \DOMDocument();
            $appConfig = AppManagerConfig::getInstance();
            $xmlFile = dirname(__DIR__, 2).DIRECTORY_SEPARATOR."{$appConfig->getContainer()}/{$this->appName}/Config/app-config.xml";
            
            if (!file_exists($xmlFile)) {
                throw new PHPBackendException('Le fichier de configuration de l\'application "'.$this->appName.'" n\'existe pas sur le serveur. =>'.$xmlFile);
            }
            /**
             * @var \DOMDocument $readFile
             */
            $readFile = $xml->load($xmlFile);
            if ($readFile===false) {
                throw new PHPBackendException('Impossible de parser le fichier de configuration de l\'application "'.$this->appName.'" => ('.$xmlFile.')');
            }
            
            /**
             * @var \DOMElement[] $users
             */
            $users = $xml->getElementsByTagName('user');
            
            foreach ($users as $user) {
                $u = new User();
                $u->setPseudo($user->getAttribute('pseudo'));
                $u->setPassword($user->getAttribute('password'));
                $this->users[] = $u;
            }
        }
        foreach ($this->users as $u) {
            if ($u->getPseudo()==$pseudo) {
                return $u;
            }
        }
        return null;
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

            /**
             * @var DOMElement $child
             */
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

