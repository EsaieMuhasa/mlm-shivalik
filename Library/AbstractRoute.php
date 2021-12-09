<?php
namespace Library;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class AbstractRoute
{
    
    /**
     * Le pattern de l'urlPattern
     * @var string
     */
    protected $urlPattern;
    
    /**
     * @var array
     */
    protected $paramsNames;
    
    /**
     * Tableau associatif cle/valeur => pour nom du parametre/ valeur du parametre
     * @var array
     */
    protected $params = array();

        
    /**
     * Constructeur d'initiaisation d'une route
     * @param string $urlPattern le parttern de l'urlPattern
     * @param array $paramsNames les noms de parrametre a inclure dans le $_GET
     */
    public function __construct(string $urlPattern, $paramsNames = array())
    {
        $this->setUrlPattern($urlPattern);
        $this->setParamsNames($paramsNames);
    }
   
    /**
     * Pour savoir si la route a des parametres
     * @return boolean
     */
    public function hasParams()
    {
        return !empty($this->paramsNames);
    }
    
    /**
     * Commparaison de l'urlPattern avec le patterne
     * @param string $url
     * @return array|boolean
     */
    public function match($url)
    {
        $match = array();
        if (preg_match('#^'.$this->urlPattern.'$#', $url, $match)) {
            return $match;
        }
        return false;
    }
    
    /**
     * @return string
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }

    /**
     * @return array
     */
    public function getParamsNames()
    {
        return $this->paramsNames;
    }

    /**
     * @return multitype:
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $urlPattern
     */
    public function setUrlPattern($urlPattern)
    {
        if (!is_string($urlPattern)) {
            throw new LibException("L'url pattern doit etre un patterne valide");
        }
        $this->urlPattern = $urlPattern;
    }

    /**
     * @param array $paramsNames
     */
    public function setParamsNames($paramsNames)
    {
        if (!is_array($paramsNames)) {
            throw new LibException("Parametre invalide. Donnez un array en parametre.");
        }
        $this->paramsNames = $paramsNames;
    }

    /**
     * @param multitype: $params
     */
    public function setParams($params)
    {
        if (!is_array($params)) {
            throw new LibException("Parametre invalide. Donnez un array en parametre.");
        }
        $this->params = $params;
    }

}

