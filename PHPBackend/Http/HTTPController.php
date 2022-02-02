<?php
namespace PHPBackend\Http;


use PHPBackend\Controller;
use PHPBackend\PHPBackendException;
use PHPBackend\Application;
use PHPBackend\Page;
use PHPBackend\Dao\DAOManagerFactory;
/**
 *
 * @author Esaie MHS
 *        
 */
abstract class HTTPController implements Controller
{
    
    /**
     * @var HTTPPage
     */
    private $page;
    
    /**
     * @var Application
     */
    private $application;
    
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
     * @var DAOManagerFactory
     */
    private $daoManager;

    use \PHPBackend\Dao\DAOAutoload;
    
    const ATT_FORM_VALIDATOR = 'formValidator';

    /**
     * Constructeur d'initialisation
     * @param Application $application
     * @param string $module
     * @param string $action
     */
    public function __construct(Application $application, string $module, string $action)
    {
        $this->application = $application;
        $this->setAction($action);
        $this->setModule($module);
        $this->page = new HTTPPage($application, $module);
        $this->daoManager = DAOManagerFactory::getInstance();
        $this->setView($action);
        $this->hydrateInterfaces($this->getDaoManager());
        $application->getHttpRequest()->addAttribute(self::ATT_VIEW_TITLE, "");
    }
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\ApplicationComponent::getApplication()
     */
    public function getApplication() : Application
    {
        return $this->application;
    }
    
    /**
     * @return \PHPBackend\Dao\DAOManagerFactory
     */
    public function getDaoManager() : DAOManagerFactory
    {
        return $this->daoManager;
    }


    /**
     * {@inheritDoc}
     * @see \PHPBackend\Controller::execute()
     */
    public function execute () : void
    {
        $methodName = 'execute'.ucfirst($this->action);
        $reflexClass = new \ReflectionClass($this);
        if (!$reflexClass->hasMethod($methodName)) {
            throw new PHPBackendException('L\'action "'.$this->action.'" n\'est pas définie dans le controleur "'.$reflexClass->getName().'".');
        }
        $reflexMethod = $reflexClass->getMethod($methodName);
        if($reflexMethod->getNumberOfParameters()==1) {
            $this->$methodName($this->getApplication()->getRequest());
        }else if ($reflexMethod->getNumberOfParameters()==2) {
            $this->$methodName($this->getApplication()->getRequest(), $this->getApplication()->getResponse());
        } elseif ($reflexMethod->getNumberOfParameters()==0) {
            $this->$methodName();
        }else throw new PHPBackendException('la methode d\'une action doit avoir 0 parametre, soit 2 parametre respectivement la requette (HTTPRequest) et la reponse (HTTPRresponse)');
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Controller::getAction()
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Controller::getModule()
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Controller::getPage()
     */
    public function getPage() : Page
    {
        return $this->page;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Controller::getView()
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $module
     */
    protected function setModule(string $module) : void
    {
        $this->module = $module;
    }

    /**
     * @param string $action
     */
    protected function setAction(string $action) : void
    {
        $this->action = $action;
    }

    /**
     * @param string $view
     */
    protected function setView(string $view) : void
    {
        $this->view = $view;
        $viewFile = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$this->getApplication()->getContainer().DIRECTORY_SEPARATOR.$this->getApplication()->getName().DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR.$this->getModule().DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.$view;
        $this->page->setViewFile($viewFile);
    }
}

