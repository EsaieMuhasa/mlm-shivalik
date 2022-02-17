<?php
namespace PHPBackend\Http;

use PHPBackend\Page;
use PHPBackend\PHPBackendException;
use PHPBackend\Application;

/**
 * 
 * @author Esaie MUHASA
 *
 */
class HTTPPage implements Page
{
    /**
     * Le nom du la fichier de la vue
     * @var string
     */
    private $viewFile;
    
    /**
     * @var string
     * le module concerner
     */
    private $module;    
    
    /**
     * @var string
     */
    private $title;
    
    private $application;
    
    /**
     * constructeur d'initialisation
     * @param HTTPApplication $application
     * @param string $module
     */
    public function __construct(HTTPApplication $application, ?string $module=null)
    {
        $this->application = $application;
        $this->module = $module;
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
     * @param string $viewFile
     */
    public function setViewFile(string $viewFile) : void
    {
        $this->viewFile = $viewFile;
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Page::getTitle()
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Page::setTitle()
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Page::getGeneratedPage()
     * revoie la vue generer au format HTML sout XML soit JSON
     */
    public function getGeneratedPage() : string
    {
        $layout = null;
        $layoutModule = null;
        $defaultLayout = dirname(__DIR__).DIRECTORY_SEPARATOR."DefaultLayouts".DIRECTORY_SEPARATOR."layout";
        
        $extension = $this->getApplication()->getHttpRequest()->getExtensionURI();
        if ($extension == 'json' || $extension == 'xml' || $extension == 'htm') {//pour les vues specifiques
            if ($extension == 'json' || $extension == 'xml') {
                $this->getApplication()->getHttpResponse()->addHeader('Content-Type: application/'.$extension.'; charset=UTF-8');
                $vue = $this->viewFile.'.'.$extension.'.php';
            }else {                
                $vue = $this->viewFile.'.php';
            }
            
            if ($this->module != null) {
                $layoutModule = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->getApplication()->getContainer().DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$this->module.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.'.$extension.'.php';
                if (!file_exists($layoutModule)) {
                    $layoutModule = null;
                }
            }
            $defaultLayout .= "{$extension}.php";
            $layout = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->getApplication()->getContainer().DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.'.$extension.'.php';
        }else {
            $vue = $this->viewFile.'.php';
            
            if ($this->module != null) {
                $layoutModule = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->getApplication()->getContainer().DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$this->module.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.php';
                if (!file_exists($layoutModule)) {
                    $layoutModule = null;
                }
            }
            $layout = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->getApplication()->getContainer().DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.php';
            $defaultLayout .= ".php";
        }
        
        if (!file_exists($vue)) {
            throw new PHPBackendException('La vue spécifique à la requêtte n\'existe pas:  "'.$vue.'"', 500);
        }
        
        //$_REQUEST = array_merge($_REQUEST, $this->attributes);
        
        ob_start();
        require_once $vue;
        $this->getApplication()->getRequest()->addAttribute(self::ATT_VIEW, ob_get_clean());
        
        if ($layoutModule != null) {//pour les module qui ont des Templates particuliers
            ob_start();
            require_once $layoutModule;
            $this->getApplication()->getRequest()->addAttribute(self::ATT_VIEW, ob_get_clean());
        }
        
        ob_start();
        file_exists($layout)? require_once $layout : require_once $defaultLayout;
        return ob_get_clean();
    }
    
    /**
     * Generation d'un page pret a etre transformer en un fichier PDF
     * @throws PHPBackendException
     * @return string
     */
    public function getGeneratedPDF () : string{
        $vue = $this->viewFile.'.pdf.php';
        $layout = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$this->getApplication()->getContainer().DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.pdf.php';
        $defaultLayout = dirname(__DIR__).DIRECTORY_SEPARATOR."DefaultLayouts".DIRECTORY_SEPARATOR."layout.pdf.php";
        
        if (!file_exists($vue)) {
            throw new PHPBackendException('La vue spécifique a la geration du PDF n\'existe pas. -> "'.$vue.'"', 500);
        }
        
        $_REQUEST = array_merge($_REQUEST, $this->attributes);
        
        ob_start();
        require_once $vue;
        $this->getApplication()->getHttpRequest()->addAttribute(self::ATT_VIEW, ob_get_clean());
        
        ob_start();
        file_exists($layout)? require_once $layout : require_once $defaultLayout;
        return ob_get_clean();
    }
    
    /**
     * Generation du contenue d'un message email
     * {@inheritDoc}
     * @see \PHPBackend\Page::getGeneratedMail()
     * @throws PHPBackendException
     */
    public function getGeneratedMail () : string{
        $vue = $this->viewFile.'.php';
        $layout = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$this->getApplication()->getContainer().DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'layout.mail.php';
        $defaultLayout = dirname(__DIR__).DIRECTORY_SEPARATOR."DefaultLayouts".DIRECTORY_SEPARATOR."layout";
        
        if (!file_exists($vue)) {
            throw new PHPBackendException('La vue spécifique a la requette n\'existe pas. "'.$vue.'"', 500);
        }
        $_REQUEST = array_merge($_REQUEST, $this->attributes);
        
        ob_start();
        require_once $vue;
        $this->getApplication()->getHttpRequest()->addAttribute(self::ATT_VIEW, ob_get_clean());
        
        ob_start();
        file_exists($layout)? require_once $layout : require_once $defaultLayout;
        return ob_get_clean();
    }
}

