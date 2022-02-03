<?php
namespace PHPBackend\Http;


use PHPBackend\Config\FilterRoute;
use PHPBackend\Config\FilterConfig;
use PHPBackend\Config;
use PHPBackend\RouteNotFoundException;
use PHPBackend\PHPBackendException;
use PHPBackend\Route;
use PHPBackend\Controller;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\AppConfig;
use PHPBackend\Config\GlobalConfig;

/**
 *
 * @author Esaie MHS
 *        
 */
class HTTPApplication implements Application
{
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $container;
    
    /**
     * @var HTTPRequest
     */
    protected $httpRequest;
    
    /**
     * @var HTTPResponse
     */
    protected $httpResponse;
    
    /**
     * @var Config
     */
    protected $config;
    
    /**
     * L'application source qui autrait fait naissance a l'actuel
     * @var Application
     */
    protected $source;
    

    /**
     * constructeur d'initialisation d'une application
     * @param string $name le nom de l'application (le nom du dossier de l'app dans le dossier contaienr)
     * @param string $container le nom du dossier contenneur des applications
     * @param Application $source l'application source dans le cas où cette application est demarrer dans le processuce d'execution
     * d'un autre application
     */
    public function __construct(string $name, string $container, ?Application $source = null)
    {
        if ($source == null) {
            $hadler = new HTTPSessionHandler();
            session_set_save_handler($hadler, true);
            session_start();
        }
        
        $this->name = $name;
        $this->container = $container;
        $this->config = AppConfig::getInstance($name);
        $this->httpRequest = new HTTPRequest($this);
        $this->httpResponse = new HTTPResponse($this);
        $this->source = $source;
        //HTTPSessionHandler::getSessions();
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getConfig()
     */
    public function getConfig() : AppConfig
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getSource()
     */
    public function getSource(): ?Application
    {
        return $this->source;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getName()
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContainer() : ?string
    {
        return $this->container;
    }

    /**
     * alias de la methode getRequest
     * @return \PHPBackend\Http\HTTPRequest
     */
    public function getHttpRequest() : HTTPRequest
    {
        return $this->httpRequest;
    }

    /**
     * alias de la method getResponse
     * @return \PHPBackend\Http\HTTPResponse
     */
    public function getHttpResponse() : HTTPResponse
    {
        return $this->httpResponse;
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getRequest()
     */
    public function getRequest(): Request
    {
        return $this->getHttpRequest();
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getResponse()
     */
    public function getResponse(): Response
    {
        return $this->getHttpResponse();
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::run()
     */
    public function run() : void {
        try {
            $controller = $this->getController();
            $controller->execute();
            $this->getResponse()->setPage($controller->getPage());
            $this->getResponse()->send();
        } catch (\RuntimeException $e) {
            if ($e instanceof RouteNotFoundException) {
                $this->getHttpResponse()->sendError($e->getMessage(),$e->getCode());
                return;
            }
            
            if (is_callable(array($e, 'toHTML'))) {                
                $this->getResponse()->sendException($e);
            }else{
                $this->getResponse()->sendException(new PHPBackendException($e->getMessage(), PHPBackendException::APP_LIB_ERROR_CODE, $e));
            }
        }
    }
    
    /**
     * Methode utilisateur de journalisation des erreurs
     * @param PHPBackendException $exception
     */
    public function logger (PHPBackendException $exception): void{
        $date = $exception->getDate()==null? new \DateTime() : $exception->getDate();
        
        $file = @fopen(dirname(__DIR__).DIRECTORY_SEPARATOR.GlobalConfig::getInstance()->getLoggDirectory().DIRECTORY_SEPARATOR.$date->format('Y-m-d').'-'.$date->getTimestamp().'.xml', 'a');
        
        $resp = @fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>'.$exception->toXML());
        if ($resp === false) {
            throw new PHPBackendException('Echec de journalisation de l\'erreur. ', 500, $exception);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getController()
     */
    public final function getController() : Controller
    {
        //charge config general
        $xmlGeneral = new \DOMDocument();
        $xmlUploadedFileGeneral = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'config.xml';
        if (!file_exists($xmlUploadedFileGeneral)) {
            throw new PHPBackendException("Le fichier de configuration global n'existe pas: {$xmlUploadedFileGeneral}");
        }
        
        /**
         * @var \DOMDocument $readUploadedFile
         */
        $readUploadedFile = $xmlGeneral->load($xmlUploadedFileGeneral);
        if ($readUploadedFile === false) {
            throw new PHPBackendException("Impossible de parser le fichier de configuration globale: {$xmlUploadedFileGeneral}");
        }
        
        $configs = $xmlGeneral->getElementsByTagName('filters');
        if ($configs->count()==1) {//la section des filtres a ete definie
            
            /**
             * @var \DOMElement $filtes
             */
            $filters = $configs->item(0)->childNodes;
            
            for ($i = 0; $i < $filters->length; $i++) {//pour chaque filtre
                /**
                 * @var \DOMNodeList $filter
                 */
                $filter = $filters->item($i);
                
                if ($filter->nodeName != 'filter') {
                    continue;
                }
                
                $fRoutes = array();//les routes d'un filtre
                $name = $filter->getAttribute('name');
                
                $filterRoutes = $filter->childNodes;
                foreach ($filterRoutes as $fr) {//pour chaque route du filtre
                    
                    if ($fr->nodeName == 'filter-route') {
                        
                        $urlPattern = $fr->getAttribute('urlPattern');
                        $priority = intval($fr->hasAttribute('priority')? $fr->getAttribute('priority') : '1');
                        $paramsNames = array();
                        if ($fr->hasAttribute('paramsNames')) {
                            $paramsNames = explode(',', $fr->getAttribute('paramsNames'));
                        }
                        
                        
                        $conf = new FilterRoute($urlPattern, $priority, $paramsNames);
                        $fRoutes[] = $conf;
                    }
                }
                
                $fConfig = new FilterConfig($name, $fRoutes);
                
                if ($fConfig->match($this->getHttpRequest()->getURI())) {//pour les filtre qui ecoute cette URL
                    $fRoute = $fConfig->getRoute($this->getHttpRequest()->getURI());
                    
                    if ($fRoute->hasParams()) {//pour la recuperation des prametre
                        $_GET = array_merge($_GET, $fRoute->getParams());
                    }
                    
                    try {
                        $this->runFilter($fConfig);//lacement du filtre                        
                    } catch (PHPBackendException $e) {
                        $this->getHttpResponse()->sendException($e);
                    }
                }
            }
        }
        //end charge config general
        
        
        $router = new \PHPBackend\Router();
        $xml = new \DOMDocument();
        $appRoutes = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->getContainer().DIRECTORY_SEPARATOR.$this->getName().DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'app-routes.xml';
        
        if (!file_exists($appRoutes)) {
            throw new PHPBackendException('Le fichier de configuration de l\'application "'.$this->getName().'" n\'existe pas >>> '.$appRoutes.'.');
        }
        
        /**
         * @var \DOMDocument $routesUploadedFile
         */
        $routesUploadedFile = $xml->load($appRoutes);
        
        if ($routesUploadedFile === false) {
            throw new PHPBackendException('Impossible de parser le fichier de configuration. Assurez-vous d\'avoir bien respecter le DTD "../../../PHPBackend/lib-dtd-conf.dtd"');
        }
        
        $modules = $xml->getElementsByTagName('module');
        
        
        foreach ($modules as $module) {
            $moduleName = $module->getAttribute('name');
            
            /**
             * @var \DOMElement[] $routes
             */
            $routes = $module->getElementsByTagName('route');
            foreach ($routes as $route)
            {
                $urlPattern = $route->getAttribute('urlPattern');
                $action = $route->getAttribute('action');
                $paramsNames = array();
                
                if ($route->hasAttribute('paramsNames')) {
                    $paramsNames = explode(',', $route->getAttribute('paramsNames'));
                }
                $router->addRoute(new Route($urlPattern, $moduleName, $action, $paramsNames));
            }
        }
        
        
        //Recuperation de la route correspondant a l'url
        $matchRoute = null;
        try {
            $matchRoute = $router->getRoute($this->httpRequest->getURI());
        } catch (RouteNotFoundException $e) {
            $this->httpResponse->sendError($e->getMessage(), $e->getCode());
        }
        
        $_GET = array_merge($_GET, $matchRoute->getParams());
        
        $controllerUploadedFile = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->getContainer().DIRECTORY_SEPARATOR.$this->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$matchRoute->getModule().DIRECTORY_SEPARATOR.$matchRoute->getModule().'Controller.php';
        if (!file_exists($controllerUploadedFile)) {
            throw new PHPBackendException('Le fichier de définition de la classe du controlleur est inaccessible. =>'.$controllerUploadedFile, 500);
        }
        
        $controllerClass = "\\{$this->getContainer()}\\{$this->getName()}\\Modules\\{$matchRoute->getModule()}\\{$matchRoute->getModule()}Controller";
        $controllerInstance = new $controllerClass($this, $matchRoute->getModule(), $matchRoute->getAction());
        return $controllerInstance;
    }
    
    /**
     * Execution du filtre
     * @param FilterConfig $config
     * @throws PHPBackendException
     */
    protected function runFilter (FilterConfig $config) : void {
        $name = $config->getName();
        $fileName = dirname(dirname(__DIR__)).str_replace("\\", DIRECTORY_SEPARATOR, $name).'.php';
        
        if (!file_exists($fileName)) {
            throw new PHPBackendException("Le fichier de definition de la classe {$name} du filtre n'existe pas: {$fileName}");
        }
        
        /**
         * @var HTTPFilter $filter
         */
        $filter = new $name($this, $config);
        $filter->doFilter($this->getHttpRequest(), $this->getHttpResponse());
    }
 

}

