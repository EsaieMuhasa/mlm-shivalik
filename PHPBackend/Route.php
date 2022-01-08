<?php
namespace PHPBackend;

/**
 *
 * @author Esaie MHS
 *        
 */
class Route extends AbstractRoute
{
    
    /**
     * @var string
     */
    protected $module;
    
    /**
     * @var string
     */
    protected $action;
    
    
    /**
     * Constructeur d'initiaisation d'une route
     * @param string $urlPattern le parttern de l'urlPattern
     * @param string $module le module conserner par la requette
     * @param string $action l'action a executer
     * @param array $paramsNames les noms de parrametre a inclure dans le $_GET
     */
    public function __construct(string $urlPattern, string $module, string $action, $paramsNames = array())
    {
        parent::__construct($urlPattern, $paramsNames);
        $this->setModule($module);
        $this->setAction($action);
    }
    

    /**
     * @param string $module
     */
    private function setModule(string $module) : void
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getModule() : string
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    private function setAction(string $action) : void
    {
        $this->action = $action;
    }

}

