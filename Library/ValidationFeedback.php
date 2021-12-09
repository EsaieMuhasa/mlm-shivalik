<?php
namespace Library;

/**
 *
 * @author Esaie MHS
 *        
 */
class ValidationFeedback extends AbstractCrypteur
{
    /**
     * Collection des message d'errors
     * @var array
     */
    private $errors;
    
    /**
     * Collection des notifications (message informatif) mais pas d'errors
     * @var array
     */
    private $messages;
    
    /**
     * Message final, en tant que result des traitement
     * @var string
     */
    private $result;
    
    /**
     * Message suplementaire au result
     * @var string
     */
    private $message;
    
    /**
     * @var AbstractFormValidator
     */
    private $formValidator;
    
    /**
     * Constructeur dinitialisation de feedback de validation
     * @param AbstractFormValidator $formValidator
     */
    public function __construct(?AbstractFormValidator $formValidator=null)
    {
        $this->errors = array();
        $this->messages = array();     
        $this->formValidator = $formValidator;
    }
    
    /**
     * Methode magique mour acceder aux elements des collection commes 
     * des popriete de cette classe.
     * @tutorial pour les clees de la collections errors, la valeur @param $name doit commecer par error,
     * pour les clee de messages, la valeur de @param $name doit commencer par message.
     * Pour tout les dexu cas le nom du parametre doit commencer par une pajuscule.
     * donc eveiter de commence les clefs par des caracetres numerique 
     * @param string $name
     * @return string|null
     */
    public function __get($name){
        $matches = array();
        if ($name == 'formValidator' && $this->formValidator!=null) {
            return $this->formValidator;
        }
        
        if (preg_match('#^error(.+)$#', $name, $matches)) {
            $shorName = lcfirst($matches[1]);
            if ($this->hasError($shorName)) {
                return $this->getErrors()[$shorName];
            }
        }elseif (preg_match('#^message(.+)$#', $name, $matches)) {
            $shorName = lcfirst($matches[1]);
            if ($this->hasMessage($shorName)) {
                return $this->getMessages()[$shorName];
            }
        }else if($name == 'result'){
            return $this->getResult();
        }else if($name == 'message'){
            return $this->getMessage();
        }else if($name == 'errors'){
            return $this->getErrors();
        }else if($name == 'messages'){
            return $this->getMessages();
        }
        return null;
    }
    
    /**
     * Verificatio si une cle existe dans l'une des collection errors ou messages
     * @param string $name
     * @return boolean
     */
    public function __isset($name){
        $matches = array();
        if (preg_match('#^error(.+)$#', $name, $matches)) {
            return $this->hasError(lcfirst($matches[1]));
        }elseif (preg_match('#^message(.+)$#', $name, $matches)) {
            return $this->hasMessage(lcfirst($matches[1]));
        }
        return false;
    }

    /**
     * @return \Library\AbstractFormValidator
     */
    public function getFormValidator()
    {
        return $this->formValidator;
    }
    

    /**
     * {@inheritDoc}
     * @see \Library\AbstractCrypteur::toJSON()
     */
    public function toJSON()
    {
        $refClass = new \ReflectionClass($this);
        $json = '{';
        $json .= '"className": "'.$refClass->getShortName().'",';
        $json .= '"result":'.($this->getResult()!=null? '"'.$this->getResult().'"': 'null').', ';
        $json .= '"message":'.($this->getMessage()!=null? '"'.$this->getMessage().'"': 'null').'';
        if ($this->hasError()){
            $json .= ', "errors" : [';
            $nombreError = count($this->getErrors());
            $numError = 1;
            foreach ($this->getErrors() as $name => $message) {
                $json .= '{"'.$name.'" : "'.$message.'"}'.($numError==$nombreError? '' : ', ');
                $numError++;
            }
            $json .= ']';
        }
        if (!empty($this->getMessages())) {
            $json .= ', "messages" : [';
            $nombreError = count($this->getMessages());
            $numError = 1;
            foreach ($this->getMessages() as $name => $message) {
                $json .= '{"'.$name.'" : "'.$message.'"}'.($numError==$nombreError? '' : ', ');
                $numError++;
            }
            $json .= ']';
        }
        $json .= '}';
        return $json;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractCrypteur::toXML()
     */
    public function toXML()
    {
        $refClass = new \ReflectionClass($this);
        $xml = '<feedback className="'.$refClass->getShortName().'" namespace="'.$refClass->getNamespaceName().'">';
        $xml .= '<result>'.$this->getResult().'</result>';
        $xml .= '<message>'.$this->getMessage().'</message>';
        if ($this->hasError()){
            $xml .= '<errors>';
            foreach ($this->getErrors() as $name => $message) {
                $xml .= '<error name="'.$name.'" value="'.$message.'"/>';
            }
            $xml .= '</errors>';
        }
        if (!empty($this->getMessages())) {
            $xml .= '<messages>';
            foreach ($this->getMessages() as $name => $message) {
                $xml .= '<message name="'.$name.'" value="'.$message.'"/>';
            }
            $xml .= '</messages>';
        }
        $xml .= '</feedback>';
        return $xml;
    }
    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param array $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
    
    
    /**
     * Ajout d'un message d'erreur lors de la validation
     * @param string $name
     * @param string $message
     * @return void
     */
    public function addError($name, $message)
    {
        $this->errors[$name] = $message;
    }
    
    /**
     * Pour verifier s'il y a eu erreur lors des traitements doit si une cles 
     * existe dans la collection des message d'errors
     * @param string $key
     * @return boolean
     */
    public function hasError($key = null)
    {
        if ($key != null) {
            return array_key_exists($key, $this->errors);
        }
        
        return !(empty($this->errors));
    }
    
    /**
     * Pour verifier s'il y a eu amoin un message informatif
     * soit si une cle existe dans la collection des message informatif
     * @param string $key
     * @return boolean
     */
    public function hasMessage($key = null){
        if ($key != null) {
            return array_key_exists($key, $this->messages);
        }
        return !(empty($this->messages));
    }
    
    /**
     * Ajout d'un message quelconque a  la collection des messages
     * @param string $name
     * @param string $message
     * @return void
     */
    public function addMessage($name, $message)
    {
        $this->messages[$name] = $message;
    }

}

