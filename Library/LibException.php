<?php
namespace Library;
/**
 *Exception specifique au fornctionnement de la bibliotheque
 *@author Esaie Muhasa
 * A chaque exeption la bibiothe que doit faire une jouralisation
 */
class LibException extends \Exception
{
    const APP_LIB_ERROR_CODE = 500;
    
    /**
     * La date ou l'exception s'est produit
     * @var \DateTime
     */
    private $date;
    
    /**
     * Constructeur herite de la classe \Exception
     * @param string $message
     * @param int $errorCode
     * @param \Exception $previous
     */
    public function __construct(?string $message, ?int $errorCode = self::APP_LIB_ERROR_CODE, $previous=null){
        parent::__construct($message, $errorCode, $previous);
        $this->date = new \DateTime();
    }
    
    
    
    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date) : ?\DateTime
    {
        $this->date = $date;
    }

    /**
     * Deserialisation d'une exception enregistrer au format XML
     * @param string $textXML
     * @return LibException
     * @throws LibException
     */
    public static function parseXML(string $textXML) : LibException {
        $xml = new \DOMDocument();
        $readable = $xml->loadXML($textXML);
        
        if ($readable===false){
            throw new LibException('Impossible de paser le contenue XML: '.$textXML);
        }
        
        $root = $xml->getElementsByTagName('exception');
        if ($root==null || empty($root)){
            throw new LibException('Les donnees XML en parametre sont invalide: '.$textXML);
        }
        
        /**
         * @var \DOMNode $roots
         */
        $root = $root->item(0);//le premier element du tableu
        $message = '';
        $code = 404;
        
        
        return new LibException($message, $code);
       
        
    }
    
    /***
     * Conversion d'une exeption au format JSON
     * @return string
     */
    public function toJSON(){
        $reflection = new \ReflectionClass($this);
        $json = '"exception" : {';
        $json .= '"className" : "'.$reflection->getName().'",';
        $json .= '"message" : "'.$this->getMessage().'"';
        $json .= '"exceptionLine" : '.$this->getLine().'';
        $json .= '"exceptionCode" : "'.$this->getCode().'"';
        
        $previous = $this->getPrevious();
        if ($previous!=null) {
            $json .= ', '.$this->previousToJSON($previous);
        }
        
        $json .= '}';
        return $json;
    }
    
    /**
     * Conversion d'une exception au format XML
     * @return string
     */
    public function toXML(){
        $reflection = new \ReflectionClass($this);
        $xml = '<exception ';
        $xml .= 'className ="'.$reflection->getName().'">';
        $xml .= '<property name="message" value="'.htmlspecialchars($this->getMessage()).'"/>';
        $xml .= '<property name="line" value="'.$this->getLine().'"/>';
        $xml .= '<property name="code" value="'.$this->getCode().'"/>';
        $xml .= '<property name="filename" value="'.$this->getFile().'"/>';
        $xml .= '<property name="date">';
        $xml .= "<date timestemp=\"{$this->date->getTimestamp()}\" shortDate=\"{$this->date->format('d-m-Y')}\" fullDate=\"{$this->date->format('d-m-Y à H\h:i')}\"/>";
        $xml .= '</property>';
        $xml .= '<property name="tracert">';
        foreach ($this->getTrace() as $tr) {
            $xml .= '<trace>';
            foreach ($tr as $key=> $t) {
                $xml .= '<param name="'.@strval($key).'" value="'.@strval($t).'"/>';
            }
            $xml .= '</trace>';
        }
        $xml .= '</property>';
        if ($this->getPrevious()!=null) {
            $xml .= '<property name="previous">';
            $xml .= self::previousToXML($this->getPrevious());
            $xml .= '</property>';
        }
        $xml .= '</exception>';
        return $xml;
    }
    
    /**
     * Methode de conversion d'une exception en code HTML
     * Cette methode est pus expoiter par la bibliotheque pour les debugage et le tracage des
     * exception.
     * Le format du code HTML generer est carement relier au code CSS de la bibioteque
     * @return string
     */
    public function toHTML(){
        $reflection = new \ReflectionClass($this);
        $html = '<ul class="list-group"> ';
        $html .= '<li> ClassName: '.$reflection->getName().' </li>';
        $html .= '<li> Message: '.$this->getMessage().'</li>';
        $html .= '<li> Code: '.$this->getCode().'</li>';
        $html .= '<li> Line: '.$this->getLine().'</li>';
        $html .= '<li> File: '.$this->getFile().'</li>';
        $html .= '<li> <div class="alert alert-danger"><pre>';
        $html .= $this->getTraceAsString();
        $html .= '</pre></div></li>';
        if ($this->getPrevious()!=null) {
            $html .= '<li> Exception Predecesseuer';
            $html .= LibException::previousToHTML($this->getPrevious());
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    
    /***
     * Convesion de toute les exception precedante au format JSON
     * @param \Exception $previous
     * @return string
     */
    protected static function previousToJSON(\Exception $previous){
        $refPrevious = new \ReflectionClass($previous);
        $json = '"previous" : {';
        $json .= '"className" : "'.$refPrevious->getName().'",';
        $json .= '"message" : "'.$previous->getMessage().'",';
        $json .= '"line" : '.$previous->getLine().',';
        $json .= '"code" : "'.$previous->getCode().'"';
        if ($previous->getPrevious()!=null) {
            $json .= ','.LibException::previousToJSON($previous->getPrevious());
        }
        $json .= '}';
        return  $json;
    }
    
    /**
     * Conversion de tout les exceptions precedante au format XML
     * @param \Exception $previous
     * @return string
     */
    protected static function previousToXML(\Exception $previous){
        $refPrevious = new \ReflectionClass($previous);
        $xml = '<exception ';
        $xml .= 'className ="'.$refPrevious->getName().'">';
        $xml .= '<property name="message" value="'.htmlspecialchars($previous->getMessage()).'"/>';
        $xml .= '<property name="line" value="'.$previous->getLine().'"/>';
        $xml .= '<property name="code" value="'.$previous->getCode().'"/>';
        if (is_callable(array($previous, 'getDate'))) {
            $xml .= '<property name="date">';
            $date = $previous->getDate();
            $xml .= "<date timestemp=\"{$date->getTimestamp()}\" shortDate=\"{$date->format('d-m-Y')}\" fullDate=\"{$date->format('d-m-Y à H\h:i')}\"/>";
            $xml .= '</property>';
        }
        $xml .= '<property name="tracert">';
        foreach ($previous->getTrace() as $tr) {
            $xml .= '<trace>';
            foreach ($tr as $key=> $t) {
                $xml .= '<param name="'.@strval($key).'" value="'.@strval($t).'"/>';
            }
            $xml .= '</trace>';
        }
        $xml .= '</property>';
        if ($previous->getPrevious()!=null) {
            $xml .= '<property name="previous">';
            $xml .= LibException::previousToXML($previous->getPrevious());
            $xml .= '</property>';
        }
        $xml .= '</exception>';
        return  $xml;
    }
    
    /**
     * Conversion d'une exception en code HTML.
     * Cette methode s'utilise conjointement avec la methode toHTML
     * @param \Exception $exception
     * @return string
     */
    protected static function previousToHTML(\Exception $previous){
        $reflection = new \ReflectionClass($previous);
        $html = '<ul> ';
        $html .= '<li> ClassName: '.$reflection->getName().' </li>';
        $html .= '<li> Message: '.$previous->getMessage().'</li>';
        $html .= '<li> Code: '.$previous->getCode().'</li>';
        $html .= '<li> Line: '.$previous->getLine().'</li>';
        $html .= '<li> File: '.$previous->getFile().'</li>';
        $html .= '<li><div class="alert alert-danger"><pre>';
        $html .= $previous->getTraceAsString();
        $html .= '</pre></div></li>';
        if ($previous->getPrevious()!=null) {
            $html .= '<li> Exception Predecesseuer';
            $html .= LibException::previousToHTML($previous->getPrevious());
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    
}

