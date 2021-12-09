<?php
namespace Library;

/**
 *
 * @author Esaie MHS
 *        
 */
class Page extends ApplicationComponent
{
    const ATT_VIEW = 'REQUEST_VIEWS_CONTENT';
    /**
     * Le nom du la fichier de la vue
     * @var string
     */
    private $viewFile;
    
    /**
     * Les parametre a envoyer a la vue
     * @var array
     */
    private $attributes = array();
    
    /**
     * @var string
     * le module concerner
     */
    private $module;    
    
    /**
     * {@inheritDoc}
     * @see \Library\ApplicationComponent::__construct()
     */
    public function __construct(\Library\Application $application, ?string $module=null)
    {
        parent::__construct($application);
        $this->module = $module;
    }

    /***
     * Ajout d'un attribut dans la page
     * @param string $atttributeName
     * @param mixed $attributeValue
     * @return void
     */
    public function addAttribute($attributeName, $attributeValue) : void
    {
        $this->attributes[$attributeName] = $attributeValue;
    }
    
    /**
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }

    /**
     * @param string $viewFile
     */
    public function setViewFile($viewFile)
    {
        $this->viewFile = $viewFile;
    }
    
    /**
     * Recuperation de la page generer sous forme de text
     * @throws LibException si la vue n'existe pas
     * @return string la page gener au format voulu (HTML, XML, JSON)
     */
    public function getGeneratedPage() : string
    {
        $layout = null;
        $layoutModule = null;
        
        $extension = $this->getApplication()->getHttpRequest()->getExtensionURI();
        if ($extension == 'json' || $extension == 'xml' || $extension == 'htm') {//pour les vues specifiques
            if ($extension == 'json' || $extension == 'xml') {
                $this->getApplication()->getHttpResponse()->addHeader('Content-Type: text/'.$extension.'; charset=UTF-8');
                $vue = $this->viewFile.'.'.$extension.'.php';
            }else {                
                $vue = $this->viewFile.'.php';
            }
            
            if ($this->module != null) {
                $layoutModule = dirname(__DIR__).DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$this->module.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.'.$extension.'.php';
                if (!file_exists($layoutModule)) {
                    $layoutModule = null;
                }
            }
            
            $layout = dirname(__DIR__).DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.'.$extension.'.php';
        }else {
            $vue = $this->viewFile.'.php';
            
            if ($this->module != null) {
                $layoutModule = dirname(__DIR__).DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$this->module.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.php';
                if (!file_exists($layoutModule)) {
                    $layoutModule = null;
                }
            }
            $layout = dirname(__DIR__).DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.php';
        }
        
        if (!file_exists($vue)) {
            throw new LibException('La vue spécifique à la requêtte n\'existe pas:  "'.$vue.'"', 500);
        }
        
        $_REQUEST = array_merge($_REQUEST, $this->attributes);
        
        ob_start();
        require_once $vue;
        $this->getApplication()->getHttpRequest()->addAttribute(self::ATT_VIEW, ob_get_clean());
        
        if ($layoutModule != null) {//pour les module qui ont des Templates particuliers
            ob_start();
            require_once $layoutModule;
            $this->getApplication()->getHttpRequest()->addAttribute(self::ATT_VIEW, ob_get_clean());
        }
        
        ob_start();
        require_once $layout;
        return ob_get_clean();
    }
    
    /**
     * Generation d'un page pret a etre transformer en un fichier PDF
     * @throws LibException
     * @return string
     */
    public function getGeneratedPDF () : string{
        $vue = $this->viewFile.'.pdf.php';
        $layout = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.pdf.php';
        
        if (!file_exists($vue)) {
            throw new LibException('La vue spécifique a la geration du PDF n\'existe pas. -> "'.$vue.'"', 500);
        }
        $_REQUEST = array_merge($_REQUEST, $this->attributes);
        
        ob_start();
        require_once $vue;
        $this->getApplication()->getHttpRequest()->addAttribute(self::ATT_VIEW, ob_get_clean());
        
        ob_start();
        require_once $layout;
        return ob_get_clean();
    }
    
    /**
     * Generation du contenue d'un message email
     * @throws LibException
     * @return string
     */
    public function getGeneratedMail () : string{
        $vue = $this->viewFile.'.php';
        $layout = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.mail.php';
        
        if (!file_exists($vue)) {
            throw new LibException('La vue spécifique a la requette n\'existe pas. -> "'.$vue.'"', 500);
        }
        $_REQUEST = array_merge($_REQUEST, $this->attributes);
        
        ob_start();
        require_once $vue;
        $this->getApplication()->getHttpRequest()->addAttribute(self::ATT_VIEW, ob_get_clean());
        
        ob_start();
        require_once $layout;
        return ob_get_clean();
    }
}

