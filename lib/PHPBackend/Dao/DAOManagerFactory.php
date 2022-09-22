<?php
namespace PHPBackend\Dao;

use PHPBackend\PDOFactory;
use PHPBackend\PHPBackendException;
use PHPBackend\Config\DBConnection;
use PHPBackend\Config\EntitiesConfig;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class DAOManagerFactory
{
    /**
     * @var DAOInterface[]
     */
    private $interfaces=array();
    
    /**
     * les metadonnees des connexion a la bdd
     * @var DBConnection[]
     */
    private $connectionsConfig = array();
    
    /**
     * @var \PDO[]
     */
    private $connections = [];
    
    /**
     * Les meta donnees de configuration des entitees
     * @var EntitiesConfig
     */
    private $entitiesConfig;
    
    /**
     * @var DAOManagerFactory
     */
    protected static $instance;
    
    /**
     * Constructeur d'initialisation
     */
    private function __construct()
    {
        $this->loadConfig();
    }
    
    /**
     * utilitaire de recuperation d'un instance du manager
     * @return DAOManagerFactory
     */
    public static final function getInstance () : DAOManagerFactory{
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * @return \PHPBackend\Config\EntitiesConfig
     */
    public function getEntitiesConfig () : ?EntitiesConfig
    {
        return $this->entitiesConfig;
    }
    
    /**
     * Lecture du fichier de configuration globale
     * @throws PHPBackendException
     * @throws DAOException
     */
    protected function loadConfig () : void{
        $configConnection = [];
        $xml = new \DOMDocument();
        $xmlFile = dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'dao.xml';
        
        if (!file_exists($xmlFile)) {
            throw new DAOException("Le fichier de configuration global n'exixte pas. {$xmlFile}");
        }
        
        /**
         * @var \DOMDocument $readFile
         */
        $readFile = $xml->load($xmlFile);
        if ($readFile===false) {
            throw new PHPBackendException("Impossible de parser le fichier globale des configurations {$xmlFile}");
        }
        
        
        
        $connections = $xml->getElementsByTagName('dbconnections');
        if ($connections->count() == 0) {
            throw new DAOException("Aucune connexion n'a été definie dans le fichier de configuration");
        }
        
        $elements = $connections[0]->childNodes;
        for ($i = 0; $i < $elements->length; $i++) {
            $element = $elements->item($i);
            if ($element->nodeName == 'connection') {
                $meta = new DBConnection($element->getAttribute('name'), $element->getAttribute('dsn'),
                    $element->getAttribute('user'), $element->getAttribute('password'));
                $configConnection[] = $meta;
            }
        }
        
        $this->connectionsConfig = $configConnection;
        $this->entitiesConfig = new EntitiesConfig($xml);//Les entities
    }
    
    /**
     * recuperation d'une connexion
     * @param string $name
     * @throws DAOException
     * @return \PDO
     */
    public function getConnection (?string $name=null) : \PDO{
        if ($name == null) {
            return $this->getFirstConnection();
        }
        
        if (!$this->connectionExists($name)) {//S'il n'y a aucune instance de cette connexion, alors on la cree
            $created = false ;
            foreach ($this->connectionsConfig as $config) {
                if ($config->getName() == $name) {
                    $created = true;
                    $this->connections[$config->getName()] = PDOFactory::getPDOInstance($config->getDsn(), $config->getUser(), $config->getPasword());
                    break;
                }
            }
            
            if (!$created) {
                throw new DAOException("Aucune connexion ne correspond a {$name} dans la configuration global des applications");
            }
        }
        
        return $this->connections[$name];
    }
    
    
    /**
     * Cette connexion existe dans la pille des connexions????
     * @param string $name
     * @return bool
     */
    public function connectionExists (string $name) : bool{
        return array_key_exists($name, $this->connections);
    }
    
    /**
     * Recuperation de la premiere connection dans la pille des connexion
     * @throws DAOException
     * @return \PDO
     */
    public function getFirstConnection () : \PDO {
        if (empty($this->connections)) {
            
            if (empty($this->connectionsConfig)) {
                throw new DAOException("Impossible d'etablire une connexion avec la base de donnee car aucunne configuration n'est disponique");
            }
            
            $first = $this->connectionsConfig[array_key_first($this->connectionsConfig)];
            $this->connections[$first->getName()] = PDOFactory::getPDOInstance($first->getDsn(), $first->getUser(), $first->getPasword());
        }
        
        return $this->connections[array_key_first($this->connections)];
    }
    
    /**
     * Recuperation du manager d'un entite
     * @param string $name
     * @throws PHPBackendException
     * @return \PHPBackend\Dao\DAOInterface|DefaultDAOInterface
     */
    public function getManagerOf($name) : DAOInterface
    {
        if (array_key_exists($name, $this->interfaces)) {
            return $this->interfaces[$name];
        }else{
            
            $meta = $this->entitiesConfig->getMetadata($name);            
            $implementation= $meta->getImplementation();
            $instance = new $implementation($this);
            
            $this->interfaces[$name] = $instance;
            return $instance;
        }
    }
    
    /**
     * Recuperation d'une instance de l'implementation de la classe dont le nom est en parametre
     * @param string $alias le nom de la classe abstraite dont on a besion de la reference vers son implementation
     * @throws PHPBackendException s'il le nom de la classe en parametre est invalide
     * @return \PHPBackend\Dao\DAOInterface
     */
    public function find ($alias) : DAOInterface
    {
        if (!$this->entitiesConfig->hasAlias($alias)) {
            throw new PHPBackendException("L'alias => '{$alias}' est inconnue dans la configuration des managers");
        }
        return $this->getManagerOf($this->entitiesConfig->findMetadata($alias)->getSimpleName());
    }
}

