<?php
namespace PHPBackend\Http;

use PHPBackend\Request;
use PHPBackend\File\UploadedFile;
use PHPBackend\Application;
use PHPBackend\Session;


/**
 *
 * @author Esaie MHS
 *        
 */
final class HTTPRequest implements Request
{    
    /**
     * @var Application
     */
    private $application;
    
    /**
     * @var HTTPSession
     */
    private $session;

    /**
     * constructeur d'initialisation
     * @param HTTPApplication $application
     */
    public function __construct(HTTPApplication $application) {
        $this->application = $application;
        $this->session = HTTPSession::getCurrent();
        $this->addAttribute(self::ATT_APP_CONFIG, $application->getConfig());
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::addToast()
     */
    public function addToast(\PHPBackend\ToastMessage $toast): void
    {
       if ($this->getSession()->hasAttribute(self::ATT_TOAST_MESSAGES)) {
           $toasts = $this->getSession()->getAttribute(self::ATT_TOAST_MESSAGES);
           array_push($toasts, $toast);
           $this->getSession()->addAttribute(self::ATT_TOAST_MESSAGES, $toasts);
       } else {
           $this->getSession()->addAttribute(self::ATT_TOAST_MESSAGES, [$toast]);
       }
       
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getMethod()
     */
    public function getMethod() : string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::existGET()
     */
    public function existGET(string $name): bool
    {
        return $this->existInGET($name);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::existPOST()
     */
    public function existPOST(string $name): bool
    {
        return $this->existInPOST($name);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::fileExist()
     */
    public function fileExist(string $name): bool
    {
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::existInFILES()
     */
    public function existInFILES(string $name): bool
    {
        return isset($_FILES[$name]);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getDataFILES()
     */
    public function getDataFILES(string $name): array
    {
        if ($this->existInFILES($name)) {
            return $_FILES[$name];
        }
        return [];
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getUploadedFile()
     */
    public function getUploadedFile(string $name): UploadedFile
    {
        return new UploadedFile($this->getDataFILES($name));
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::attributeExist()
     */
    public function attributeExist(string $name): bool
    {
        return isset($_REQUEST[$name]);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\ApplicationComponent::getApplication()
     */
    public function getApplication(): Application
    {
        return $this->application;
    }


    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getAttributes()
     */
    public function getAttributes(): array
    {
        return $_REQUEST;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getURI()
     */
    public function getURI(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::removeAttribute()
     */
    public function removeAttribute(string $name): void
    {
        if ($this->attributeExist($name)) {
           unset($_REQUEST[$name]);
        }
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getExtensionURI()
     */
    public function getExtensionURI() : ?string{
        $url = $this->getURI();
        $matches = array();
        if (preg_match('#^(.+)\.([a-zA-Z0-9]{1,5})$#', $url, $matches)){
            return $matches[2];
        }
        return null;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::addDataPOST()
     */
    public function addDataPOST (string $name, $data) : Request{
        $_POST[$name] = $data;
        return $this;
    }
    
    /**
     * Verification si le cookie existe.
     * le cookie est verfier dans le contexte de l'application en cours
     * @param string $name
     * @return bool
     */
    public function cookieExists(string $name) : bool
    {
        return isset($_COOKIE[$this->getApplication()->getName().'__'.$name]);
    }
    
    /**
     * Recuperation d'une cookie dans la requette
     * @param string $name le nom du cookie
     * @return \PHPBackend\Http\HTTPCookie|NULL
     */
    public function getCookie($name) : ?HTTPCookie
    {
        if ($this->cookieExists($name)) {
            return HTTPCookie::buildCookie($this->getApplication(), $name);
        }
        return null;
    }
    
    /**
     * Recuperation de tout les Cookies qui sont dans la requette
     * dans ce cas on ne tiens pas compte du contexte de l'application encours d'execution
     * @return \PHPBackend\Http\HTTPCookie[]
     */
    public function getCookies()
    {
        $cookies = array();
        foreach ($_COOKIE as $name => $data) {
            $cookies[] = HTTPCookie::buildForData($this->getApplication(), $name, $data);
        }
        return $cookies;
    }
    
    /**
     * Recuperation d'une valeur dans le $_GET
     * {@inheritDoc}
     * @see \PHPBackend\Request::getDataGET()
     */
    public function getDataGET(string $name)
    {
        return ($this->existGET($name) && !empty($_GET[$name]))? $_GET[$name] : null;
    }
    

    /**
     * Recuperation de la  donnee dans la porte POST
     * {@inheritDoc}
     * @see \PHPBackend\Request::getDataPOST()
     */
    public function getDataPOST(string $name)
    {
        return ($this->existPOST($name) && !empty($_POST[$name]))? $_POST[$name] : null;
    }
    
    /**
     * Verrification de l'existance d'une donnee dans le $_POST
     * {@inheritDoc}
     * @see \PHPBackend\Request::existInPOST()
     */
    public function existInPOST(string $name) : bool
    {
        return (isset($_POST[$name]));
    }
    

    /**
     * Verification de l'existance d'une donnee dans le $_GET
     * {@inheritDoc}
     * @see \PHPBackend\Request::existGET()
     */
    public function existInGET(string $name) : bool
    {
        return isset($_GET[$name]);
    }
    
    /**
     * Redirection interne dans l'application ou entre application
     * {@inheritDoc}
     * @see \PHPBackend\Request::forward()
     */
    public function forward(string $action, ?string $module=null, ?string $applicationName=null) : void
    {
        if ($applicationName==null) {
            $controllerClass = "{$this->getApplication()->getContainer()}\\{$this->getApplication()->getName()}\\Modules\\{$module}\\{$module}Controller";
            /**
             * @var HTTPController $constrollerInstance
             */
            $controllerInstance = new $controllerClass($this->getApplication(), $module, $action);
            $controllerInstance->execute();
            $this->getApplication()->getResponse()->setPage($controllerInstance->getPage());
            $this->getApplication()->getResponse()->send();
            return;
        }
        
        $applicationInstance = new HTTPApplication($applicationName, $this->getApplication()->getContainer(), $this->getApplication());
        $controllerClass = "\\{$applicationInstance->getContainer()}\\{$applicationInstance->getName()}\\Modules\\{$module}\\{$module}Controller";
        
        $controllerInstance = new $controllerClass($applicationInstance, $module, $action);
        $controllerInstance->execute();
        $applicationInstance->getResponse()->setPage($controllerInstance->getPage());
        $applicationInstance->getResponse()->send();
    }
    
    /**
     * homologation de l'action
     * @param string $action
     * @param string $module
     * @param string $application
     */
    public function triggered (string $action, ?string $module=null, ?string $application=null) : void{
        
    }
    
    /**
     * Ajout d'un attribut dans la portee $_REQUEST
     * @param string $name le nom de l'attribut
     * @param string $value la valuer de l'attribut
     * @return void
     */
    public function addAttribute($name, $value) : void
    {
        $_REQUEST[$name] = $value;
    }
    
    /**
     * Recuperation d'un attribut dans la portee $_REQUEST
     * @param string $name le nom de l'attribut
     * @return NULL|mixed particulierement null quand l'attribut n'existe pas
     */
    public function getAttribute($name)
    {
        return $this->existAttribute($name)? $_REQUEST[$name] : null;
    }
    
    /**
     * Verification de l'existance d'un attribut dans la portee $_REQUEST
     * @param string $name le nom de l'attribut dans la dite portee
     * @return boolean
     */
    public function existAttribute($name) : bool
    {
        return isset($_REQUEST[$name]);
    }
    
    /**
     * Supression d'un attribut dans $_REQUEST
     * @param string $name
     * @return void
     */
    public function deleteAttribute($name) : void
    {
        if ($this->existAttribute($name)) {
            unset($_REQUEST[$name]);
        }
    }
    
    /**
     * 
     * @param string $key
     * @return boolean
     */
    public function existFILE($key) : bool{
        return (isset($_FILES[$key]));
    }
    
    /**
     * @param string $key
     * @return \PHPBackend\File\UploadedFile
     */
    public function getUloadedFile($key){
        $file= new UploadedFile($this->existInFILE($key)? $_FILES[$key] : array());
        return $file;
    }
    
    /**
     * Ajout d'un parametre dans le super global $_GET
     * @param string $name
     * @param mixed $value
     */
    public function addParam(string $name, $value) : void
    {
        $_GET[$name] = $value;
    }
    
    /**
     * Suression d'un parametre dans le $_GET
     * @param string $name
     */
    public function deleteParam(string $name) : void
    {
        if ($this->existGET($name)) {
            unset($_GET[$name]);
        }
    }
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getSession()
     */
    public function getSession(): Session
    {
        return $this->session;
    }

}
