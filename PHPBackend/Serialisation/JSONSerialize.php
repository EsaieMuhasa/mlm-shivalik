<?php
namespace PHPBackend\Serialisation;


use PHPBackend\Validator\DefaultFormValidator;

/**
 *
 * @author Esaie MHS
 *        
 */
trait JSONSerialize
{
    /**
     * @param string $stringJson
     * @return object
     */
    public static function fromJSON (string $stringJson) {
        
    }
    
    /**
     * Pour la conversion d'un objet au format JSON
     * @return string
     */
    public function toJSON() : string
    {
        $refClass = new \ReflectionClass($this);
        $json = '{';
        /**
         * @var \ReflectionProperty[] $properties
         */
        $properties = $refClass->getProperties();
        $nombrePropeties = count($properties);
        
        //le metadonnees de la classe
        $json.='"metadatas" : {';
        $json.='"classFullName" : "'.str_replace('\\', '\\\\', $refClass->getName()).'",';
        $json.='"className" : "'.$refClass->getShortName().'",';
        $json.='"namespace" : "'.str_replace('\\', '\\\\', $refClass->getNamespaceName()).'"';
        $json.='}';
        
        foreach ($properties as $numero => $properie)
        {//Iteration des poropriete d'un objet
            $methodGET = 'get'.ucfirst($properie->getName());
            $methodIS = 'is'.ucfirst($properie->getName());
            
            $method = (method_exists($this, $methodGET))? ($methodGET) : (is_callable(array($this, $methodIS))? $methodIS : null);
            
            if ($method == null || $this->$method() == null) {//elimination des proprietes qui n'ont pas de valeur
                continue;
            }
            
            $json .= ', "'.$properie->getName().'": ';
            
            if (method_exists($this, $methodGET) && is_object($this->$methodGET())) {//Pour les objets
                if (is_callable(array($this->$methodGET(), 'toJSON'))) {
                    $json .= $this->$methodGET()->toJSON();
                }elseif ($this->$methodGET() instanceof \DateTime){
                    $json .= '"'.$this->$methodGET()->format('d/m/Y à H\h:i:s').'"';
                }
            }elseif (method_exists($this, $methodGET) && is_array($this->$methodGET())){//Pour une collections des objets
                $sizeArray = count($this->$methodGET());
                $nTour = 1;
                if ($sizeArray!=0) {
                    $isObject = false;
                    
                    if (!preg_match(DefaultFormValidator::RGX_INT, array_key_first($this->$methodGET()))) {
                        $isObject = true;
                    }
                    
                    $json .= $isObject? '{' : '[';
                    
                    foreach ($this->$methodGET() as $key => $item) {
                        
                        $json .= $isObject? '"'.$key.'" : ' : '';//pour les objest literal
                        
                        if (is_object($item)) {//Pour les objets qui sont dans le tableau
                            
                            if (is_callable(array($item, 'toJSON'))) {
                                $json .= $item->toJSON();
                            }elseif ($item instanceof \DateTime){
                                $json .= '"'.$item->format('d/m/Y à H\h:i:s').'"';
                            }elseif($item==null){
                                $json .= 'null';
                            }else{
                                $json .= '"objet non pris en charge"';
                            }
                        }else{
                            if (is_numeric($item) && !preg_match('#^0.*#', $item)) {
                                $json .= $item;
                            }elseif (is_bool($item)){
                                $json .= $item==true? 'true' : 'false';
                            }elseif($item==null){
                                $json .= 'null';
                            }else{
                                $json .= '"'.$item.'"';
                            }
                        }
                        $json .= ($nTour!=$sizeArray? ', ' : '');
                        $nTour++;
                    }
                    $json .= $isObject? '}' : ']';
                }else{
                    $json .= '[]';
                }
            }elseif(method_exists($this, $methodIS)){//Pour les booleans
                $json .= ($this->$methodIS()==true? 'true':'false');
            }else{//Pour tout les propriete simple ou dont leur valeur veau null
                if (method_exists($this, $methodGET)) {
                    if ((is_numeric($this->$methodGET()) || is_bool($this->$methodGET())) 
                        && !preg_match(DefaultFormValidator::RGX_TELEPHONE_RDC, $this->$methodGET())  && !preg_match(DefaultFormValidator::RGX_TELEPHONE, $this->$methodGET())) {
                        $json .= is_bool($this->$methodGET())? ($this->$methodGET()==true? 'true':'false'): $this->$methodGET();
                    }elseif($this->$methodGET()==null){
                        $json .= 'null';
                    }else{
                        $json .= '"'.$this->$methodGET().'"';
                    }
                }else {
                    $json .= 'null';
                }
            }
            
            if ($numero!=($nombrePropeties-1)) {
                $json .= '';
            }
        }
        $json .= '}';
        return $json;
    }
    
}

