<?php
namespace Library;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class Controller extends ApplicationComponent
{
    const ATT_VIEW_TITLE = 'TITLE_VIEW';
    /**
     * @var Page
     */
    private $page;
    
    /**
     * @var string
     */
    private $module;
    
    /**
     * @var string
     */
    private $action;
    
    /**
     * @var string
     */
    private $view;
    
    /**
     * @var DAOManager
     */
    private $daoManager;

    use DAOAutoload;
    
    const ATT_FORM_VALIDATOR = 'formValidator';

    /**
     * Constructeur d'initialisation
     * @param Application $application
     * @param string $action
     * @param string $module
     */
    public function __construct(Application $application, $action, $module)
    {
        parent::__construct($application);
        $this->setAction($action);
        $this->setModule($module);
        $this->page = new Page($application, $module);
        $this->setView($action);
        $this->daoManager = DAOManager::getInstance();
        $this->autoHydrate($this->getDaoManager());
        $application->getHttpRequest()->addAttribute(self::ATT_VIEW_TITLE, "");
    }
    
    /**
     * @return \Library\DAOManager
     */
    public function getDaoManager()
    {
        return $this->daoManager;
    }

    /**
     * Execution de l'action du cotrolleur
     * @throws LibException
     */
    public function execute() : void
    {
        $methodName = 'execute'.ucfirst($this->action);
        $reflexClass = new \ReflectionClass($this);
        if (!$reflexClass->hasMethod($methodName)) {
            throw new LibException('L\'action "'.$this->action.'" n\'est pas définie dans le controleur "'.$reflexClass->getName().'".');
        }
        $reflexMethod = $reflexClass->getMethod($methodName);
        if ($reflexMethod->getNumberOfParameters()==2) {
            $this->$methodName($this->getApplication()->getHttpRequest(), $this->getApplication()->getHttpResponse());
        } elseif ($reflexMethod->getNumberOfParameters()==0) {
            $this->$methodName();
        }else throw new LibException('la methode d\'une action doit avoir 0 parametre, soit 2 parametre respectivement la requette (HTTPRequest) et la reponse (HTTPRresponse)');
        
    }
    
    /**
     * @return \Library\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        if (!is_string($module)) {
            throw new LibException('Le module doit etre une chaine de caractere valide', 500);
        }
        $this->module = $module;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        if (!is_string($action)) {
            throw new LibException('L\'action doit etre une chaine de caractere valide', 500);
        }
        $this->action = $action;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        if (!is_string($view)) {
            throw new LibException('Le nom de la vue doit etre une chaine de carractere valide.', 500);
        }
        $this->view = $view;
        $viewFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'Applications'.DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$this->getModule().DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.$view;
        $this->page->setViewFile($viewFile);
    }
}

