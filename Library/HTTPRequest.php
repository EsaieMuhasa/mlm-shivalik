<?php
namespace Library;

/**
 *
 * @author Esaie MHS
 *        
 */
final class HTTPRequest extends ApplicationComponent
{
    const HTTP_POST ='POST';
    const HTTP_GET ='GET';
    
    const ATT_APP_MESSAGES = 'PANIER_SESSION_APP_MESSAGES';


    /**
     * Recuperation de la methode HTTP d'envoie de la requette
     * @return string
     */
    public function getMethod() : string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Revoie l'url de la requette
     * @return string
     */
    public function getURI () : ?string
    {
        return $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Recuperation de l'exension de l'URL
     * @return string|NULL
     */
    public function getExtensionURI() : ?string{
        $url = $this->getURI();
        $matches = array();
        if (preg_match('#^(.+)\.([a-zA-Z]{2,5})$#', $url, $matches)){
            return $matches[2];
        }
        return null;
    }
    
    /**
     * Ajout d'un message dans la fils d'attente, des messages dans la session de l'utilisateur
     * @param AppMessage $message
     * @return HTTPRequest
     */
    public function addAppMessage (AppMessage $message) : HTTPRequest{
        if (!isset($_SESSION[self::ATT_APP_MESSAGES])) {
            $_SESSION[self::ATT_APP_MESSAGES] = array();
        }
        $_SESSION[self::ATT_APP_MESSAGES][] = $message;
        return $this;
    }
    
    /**
     * Ajout d'un information dans le $_POST
     * Si la cle existe deja dans le poste, les donnees serons ecraser
     * @param string $name
     * @param mixed $data
     * @return HTTPRequest
     */
    public function addDataPOST (string $name, $data) : HTTPRequest{
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
     * @return \Library\Cookie|NULL
     */
    public function getCookie($name) : ?Cookie
    {
        if ($this->cookieExists($name)) {
            return Cookie::buildCookie($this->getApplication(), $name);
        }
        return null;
    }
    
    /**
     * Recuperation de tout les Cookies qui sont dans la requette
     * dans ce cas on ne tiens pas compte du contexte de l'application encours d'execution
     * @return \Library\Cookie[]
     */
    public function getCookies()
    {
        $cookies = array();
        foreach ($_COOKIE as $name => $data) {
            $cookies[] = Cookie::buildForData($this->getApplication(), $name, $data);
        }
        return $cookies;
    }
    
    /**
     * Recuperation d'une valeur dans le $_GET
     * @param string $name le nom de la variable
     * @return NULL|mixed
     */
    public function getDataGET($name)
    {
        return ($this->existGET($name) && !empty($_GET[$name]))? $_GET[$name] : null;
    }
    
    /**
     * Recuperation de la  donnee dans la porte POST
     * @param string $name le nom de la variable dans la porte POST
     * @return NULL|mixed
     */
    public function getDataPOST($name)
    {
        return ($this->existPOST($name) && !empty($_POST[$name]))? $_POST[$name] : null;
    }
    
    /**
     * Verrification de l'existance d'une donnee dans le $_POST
     * @param string $name le nom de la donnee
     * @return boolean
     */
    public function existPOST($name)
    {
        return (isset($_POST[$name]));
    }
    
    /**
     * Verification de l'existance d'une donnee dans le $_GET
     * @param string $name le nom de la donnee dans le $_GET
     * @return boolean
     */
    public function existGET($name)
    {
        return isset($_GET[$name]);
    }
    
    /**
     * Redirection interne dans l'application ou entre application
     * @param string $action l'action a executer
     * @param string $module le module sur le quel l'qction doit etre executer
     * @param string $applicationName le nom de l'application recepteur le la requette de rediection
     * @throws LibException
     * @return void
     */
    public function forward($action, $module, $applicationName=null)
    {
        if ($applicationName==null) {
            $controllerClass = '\\Applications\\'.$this->getApplication()->getName().'\\Modules\\'.$module.'\\'.$module.'Controller';
            /**
             * @var Controller $constrollerInstance
             */
            $controllerInstance = new $controllerClass($this->getApplication(), $action, $module);
            $controllerInstance->execute();
            $this->getApplication()->getHttpResponse()->setPage($controllerInstance->getPage());
            $this->getApplication()->getHttpResponse()->send();
            return;
        }else {
            $applicationClass  = '\\Applications\\'.$applicationName.'\\'.$applicationName.'Application';
            
            /**
             * @var Application $applicationInstance
             */
            $applicationInstance = new $applicationClass();
            $controllerClass = '\\Applications\\'.$applicationInstance->getName().'\\Modules\\'.$module.'\\'.$module.'Controller';
            
            $controllerInstance = new $controllerClass($applicationInstance, $action, $module);
            $controllerInstance->execute();
            $applicationInstance->getHttpResponse()->setPage($controllerInstance->getPage());
            $applicationInstance->getHttpResponse()->send();
            return;
        }
    }
    
    /**
     * Ajout d'un attribut dans la portee $_REQUEST
     * @param string $name le nom de l'attribut
     * @param string $value la valuer de l'attribut
     * @return void
     */
    public function addAttribute($name, $value)
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
    public function existAttribute($name)
    {
        return isset($_REQUEST[$name]);
    }
    
    /**
     * Supression d'un attribut dans $_REQUEST
     * @param string $name
     * @return void
     */
    public function deleteAttribute($name)
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
     * @return \Library\File
     */
    public function getFile($key){
        $file= new File($this->getApplication());
        $file->initData($this->existFILE($key)? $_FILES[$key] : array());
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
}
