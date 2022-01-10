<?php
namespace PHPBackend;

use PHPBackend\File\UploadedFile;

/**
 * @author Esaie MUHASA
 */
interface Request extends ApplicationComponent
{
    const HTTP_POST ='POST';
    const HTTP_GET ='GET';
    const ATT_APP_CONFIG = "PHP_BACKEND_CURRENT_APP_COFIGURATION";
    const ATT_TOAST_MESSAGES = "PHP_BACKEND_TOAST_MESSAGES";
    
    /**
     * revoie voie une donnees associer au cle en parametre dans le GET
     * @param string $name
     */
    public function getDataGET (string $name);
    
    /**
     * Revoie la donnee associer au clee en parametre dans le POST
     * @param string $name
     */
    public function getDataPOST (string $name);
    
    /**
     * est-ce que cette donnees existe dans le POST??
     * @param string $name
     * @return bool
     */
    public function existInPOST (string $name) : bool;
    
    /**
     * Ajout d'un information dans le $_POST
     * Si la cle existe deja dans le poste, les donnees serons ecraser
     * @param string $name
     * @param mixed $data
     * @return Request
     */
    public function addDataPOST (string $name, $data) : Request;
    
    /**
     * verifie si le fichier existe
     * @param string $name
     * @return bool
     */
    public function existInFILES (string $name) : bool;
    
    /**
     * renvoie les informations sur un fichier telecharger.
     * les informatios qui sont dans le tableau retourner sont ceux du fameux tableau retourner par $_FILES['filename'];
     * @param string $name
     * @return array|NULL
     */
    public function getDataFILES (string $name) : array;
    
    /**
     * Renvoie les informations du fichier telecharger
     * @param string $name
     * @return UploadedFile
     */
    public function getUploadedFile (string $name) : UploadedFile;
    
    /**
     * esque cette donnnees exuste dans le GET??
     * @param string $name
     * @return bool
     */
    public function existInGET (string $name) : bool;
    
    /**
     * esque cette donnees exste dans la collection des attribut
     * @param string $name
     * @return bool
     */
    public function attributeExist (string $name) : bool;
    
    /**
     * revoie l'URL associer a la requette
     * @return string
     */
    public function getURI () : string;
    
    /**
     * Evoie d'un message de toast, lors de la generation de la vue
     * @param ToastMessage $toast
     */
    public function addToast (ToastMessage $toast) : void;
    
    /**
     * renvoie l'extension de l'URL
     * @return string|NULL
     */
    public function getExtensionURI() : ?string;
    
    /**
     * Revoie l'a methode utiliser pour l'evoie de cette requette par le client
     * (Ex: pour HTTP, nous avons GET, POST, PUT, ...)
     * @return string
     */
    public function getMethod () : string;
    
    /**
     * redirection interne d'une requette
     * @tutorial le parametre module et le nom de l'application ne sont pas obligatorie.
     * si ceux-ci ne sont pas specifier, alors la requette cera rediriger dans le meme module/application
     * @param string $action
     * @param string $module
     * @param string $applicationName
     * @throws PHPBackendException
     */
    public function forward (string $action, ?string $module=null, ?string $applicationName=null) : void;
    
    /**
     * ajout d'un attribut dans la collection des attributs
     * @param string $name
     * @param mixed $value
     */
    public function addAttribute (string $name, $value) : void;
    
    /**
     * revoie l'attribut associer au name en parametre
     * @param string $name
     * @return mixed|null
     */
    public function getAttribute (string $name) ;
    
    /**
     * Revoie la collection des attributs dans la requette
     * @return array
     */
    public function getAttributes () : array;
    
    /**
     * supprime un attribut dans la collection des attribut
     * @param string $name
     */
    public function removeAttribute (string $name) : void;
    
    /**
     * Renvoie la session actuel
     * @return Session
     */
    public function getSession () : Session;
    
}

