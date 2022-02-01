<?php
namespace PHPBackend\Dao;


/**
 *
 * @author Esaie MUHASA
 *        
 */
class DAOEvent
{
    const TYPE_CREATION = 1;
    const TYPE_UPDATION = 2;
    const TYPE_DELETION = 3;
    
    const TYPE_SIGLE_SELECTION = 4;
    const TYPE_MILTI_SELECTION = 5;
    
    /**
     * les donnees incapsullee dans l'evenement
     * @var mixed
     */
    private $data;
    
    /**
     * le type de levenement
     * @var int
     */
    private $type;
    
    /**
     * la source de l'evenement
     * @var mixed
     */
    private $source;
    
    /**
     * l'identifiant de l'evenement
     * @var int
     */
    private $requestId;
    
    /**
     * constructeur d'initialisation
     * @param mixed $source
     * @param int $type
     * @param mixed $data
     * @param int $requestId
     */
    public function __construct($source, int $type, $data, int $requestId  = 0) {
        $this->source = $source;
        $this->type = $type;
        $this->data = $data;
        $this->requestId = $requestId;
    }
    
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return number
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return number
     */
    public function getRequestId()
    {
        return $this->requestId;
    }
    
    /**
     * est-ce une exception???
     * @return bool
     */
    public function isException  () : bool {
        return $this->getData() instanceof DAOException;
    }

}

