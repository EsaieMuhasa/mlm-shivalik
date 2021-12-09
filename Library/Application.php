<?php
namespace Library;


use Library\Config\FilterRoute;
use Library\Config\FilterConfig;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class Application
{
    /**
     * @var string
     */
    protected $name;
    
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
     * Constructeur par defaut
     */
    public function __construct()
    {
        $this->name = null;
        $this->httpRequest = new HTTPRequest($this);
        $this->httpResponse = new HTTPResponse($this);
        $this->config = new Config($this);
    }
    
    /**
     * @return \Library\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Library\HTTPRequest
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * @return \Library\HTTPResponse
     */
    public function getHttpResponse()
    {
        return $this->httpResponse;
    }
    
    /**
     * Pour lancer l'execution de l'application
     * @return void
     */
    public function run(){
        try {
            $controller = $this->getController();
            $controller->execute();
            $this->httpResponse->setPage($controller->getPage());
            $this->httpResponse->send();
        } catch (\Exception $e) {
            
            if ($e instanceof RouteNotFoundException) {
                $this->getHttpResponse()->sendError($e->getMessage(),$e->getCode());
                return;
            }
            
            if (is_callable(array($e, 'toHTML'))) {                
                $this->httpResponse->sendException($e);
            }else{
                $this->httpResponse->sendException(new LibException($e->getMessage(), LibException::APP_LIB_ERROR_CODE, $e));
            }
        }
    }
    
    /**
     * Methode utilisateur de journalisation des erreurs
     * @param LibException $exception
     */
    public function logger (LibException $exception): void{
        $date = $exception->getDate()==null? new \DateTime() : $exception->getDate();
        $file = @fopen(dirname(__DIR__).DIRECTORY_SEPARATOR.'Web'.DIRECTORY_SEPARATOR.'logger'.DIRECTORY_SEPARATOR.$date->format('Y-m-d').'-'.$date->getTimestamp().'.xml', 'a');
        
        $resp = @fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>'.$exception->toXML());
        if ($resp === false) {
            throw new LibException('Echec de journalisation de l\'erreur. ', 500, $exception);
        }
    }
    
    /**
     * Recuperation du controlleur qui prendrant en charge la requette
     * @throws LibException
     * @return Controller
     */
    public final function getController() : Controller
    {
        //charge config general
        $xmlGeneral = new \DOMDocument();
        $xmlFileGeneral = dirname(__DIR__).'/Config/config.xml';
        if (!file_exists($xmlFileGeneral)) {
            throw new LibException("Le fichier de configuration global n'existe pas: {$xmlFileGeneral}");
        }
        
        /**
         * @var \DOMDocument $readFile
         */
        $readFile = $xmlGeneral->load($xmlFileGeneral);
        if ($readFile===false) {
            throw new LibException("Impossible de parser le fichier de configuration globale: {$xmlFileGeneral}");
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
                    
//                         var_dump($conf);
//                         exit();
                    $fRoute = $fConfig->getRoute($this->getHttpRequest()->getURI());
                    
                    if ($fRoute->hasParams()) {//pour la recuperation des prametre
                        $_GET = array_merge($_GET, $fRoute->getParams());
                    }
                    
                    try {
                        $this->runFilter($fConfig);//lacement du filtre                        
                    } catch (LibException $e) {
                        $this->getHttpResponse()->sendException($e);
                    }
                }
                
            }
        }
        //end charge config general
        
        
        $router = new \Library\Router();
        $xml = new \DOMDocument();
        $appRoutes = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getName().DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'app-routes.xml';
        
        if (!file_exists($appRoutes)) {
            throw new LibException('Le fichier de configuration de l\'application "'.$this->getName().'" n\'existe pas >>> '.$appRoutes.'.');
        }
        
        /**
         * @var \DOMDocument $routesFile
         */
        $routesFile = $xml->load($appRoutes);
        
        if ($routesFile===false) {
            throw new LibException('Impossible de parser le fichier de configuration. Assurez-vous d\'avoir bien respecter le DTD "../../../Library/lib-dtd-conf.dtd"');
        }
        
        /**
         * @var \DOMElement[] $routes
         */
        $routes =$xml->getElementsByTagName('route');
        
        foreach ($routes as $route)
        {
            $urlPattern = $route->getAttribute('urlPattern');
            $action = $route->getAttribute('action');
            $module = $route->getAttribute('module');
            $paramsNames = array();
            
            if ($route->hasAttribute('paramsNames')) {
                $paramsNames = explode(',', $route->getAttribute('paramsNames'));
            }
            
            $router->addRoute(new Route($urlPattern, $module, $action, $paramsNames));
        }
        
        //Recuperation de la route correspondant a l'url
        $matchRoute = null;
        try {
            $matchRoute = $router->getRoute($this->httpRequest->getURI());
        } catch (RouteNotFoundException $e) {
            $this->httpResponse->sendError($e->getMessage(), $e->getCode());
        }
        
        $_GET = array_merge($_GET, $matchRoute->getParams());
        
        $controllerFile = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$matchRoute->getModule().DIRECTORY_SEPARATOR.$matchRoute->getModule().'Controller.php';
        if (!file_exists($controllerFile)) {
            throw new LibException('Le fichier de définition de la classe du controlleur est inaccessible. =>'.$controllerFile, 500);
        }
        $controllerClass = '\\Applications\\'.$this->getName().'\\Modules\\'.$matchRoute->getModule().'\\'.$matchRoute->getModule().'Controller';
        $controllerInstance = new $controllerClass($this, $matchRoute->getAction(), $matchRoute->getModule());
        return $controllerInstance;
    }
    
    /**
     * Execution du filtre
     * @param FilterConfig $config
     * @throws LibException
     */
    protected function runFilter (FilterConfig $config) : void {
        $name = $config->getName();
        $fileName = dirname(__DIR__).str_replace("\\", DIRECTORY_SEPARATOR, $name).'.php';
        
        if (!file_exists($fileName)) {
            throw new LibException("Le fichier de definition de la classe {$name} du filtre n'existe pas: {$fileName}");
        }
        
        /**
         * @var Filter $filter
         */
        $filter = new $name($this, $config);
        $filter->doFilter($this->getHttpRequest(), $this->getHttpResponse());
    }
    
    /**
     * Ecriture d'un fichier sur le disque dur
     * @param File $file
     * @param string $name le nom du finchier
     * le nom du fichier est relatif a la racine des fichier pubiques
     * donc le dossier Web
     */
    public function writeFile(File $file, $name=null){
        $dir = dirname(__DIR__).DIRECTORY_SEPARATOR.($this->config->get('webData')!=null? $this->config->get('webData') : 'Web');
        
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $fileName = $dir.DIRECTORY_SEPARATOR.($name==null? ($file->getName()) : $name);
        
        if(!(move_uploaded_file($file->getTmpName(), $fileName))){
            throw new IllegalFormValueException('Erreur de configuration du serveur. tmpfile_name = '.$file->getTmpName().'; filename='.$fileName.'. Echec de recuperation du fichier dans le tmp du serveur');
        }
    }
    
    /**
     * Revoie le chemain absolut sur le serveur du dossie alouer au donnees bruts
     * @return string|NULL
     */
    public function getWebData () : ?string{
        $dir = dirname(__DIR__).DIRECTORY_SEPARATOR.($this->config->get('webData')!=null? $this->config->get('webData') : 'Web');
        
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        
        return $dir;
    }

    
    /**
     * Pour l'ecriture d'un fichier
     * @param string $name, le nom a donner au fichier
     * @param File $file
     * @return void
     * @throws IllegalFormValueException, si une erreur surviens lors de l'ecriture du fichier
     */
    public function addFile(File $file, $name=null){
        $dir = dirname(__DIR__).DIRECTORY_SEPARATOR.($this->config->get('webData')!=null? $this->config->get('webData') : 'Web').DIRECTORY_SEPARATOR.'donnee-bruts'.DIRECTORY_SEPARATOR.($file->isImage()? 'img':'files').DIRECTORY_SEPARATOR;
        
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        if(!(@move_uploaded_file($file->getTmpName(), $dir.DIRECTORY_SEPARATOR.basename($name==null? ($file->getName()) : ($name))))){
            throw new IllegalFormValueException('Erreur de configuration du serveur. tmpfile_name = '.$file->getTmpName().'. Echec de recuperation du fichier dans le tmp du serveur');
        }
    }
    
    /**
     * Pour la supression d'un fichier sur le serveur
     * @param string $name
     * @return void
     */
    public function deleteFile($name){
        if (file_exists(__DIR__.'/../Web/img/'.$name)) {
            unlink(__DIR__.'/../Web/img/'.$name);
        }elseif (file_exists(__DIR__.'/../Web/files/'.$name)) {
            unlink(__DIR__.'/../Web/files/'.$name);
        }
    }
    
    
    /**
     * Creation d'un repertoire dans le dossier publique
     * @param string $name
     */
    public function createDirectory($name){
        if (!$this->publicDirExist($name)) {
            $dirName = dirname(__DIR__).'/Web/'.$name;
            mkdir($dirName);
        }
    }
    
    /**
     * @param string $name
     * @param boolean $verifContent
     */
    public function removeDirectory($name, $verifContent=true){
        
    }

}

