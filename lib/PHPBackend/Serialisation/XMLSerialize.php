<?php
namespace PHPBackend\Serialisation;

/**
 *
 * @author Esaie MHS
 *        
 */
trait XMLSerialize
{
    /**
     * Pour convertir un objet en un text au format XML
     * Cela est utile lorsque qu'on utilise du JS pour recuperrer par AJAX
     * Cela nous sera aussi utile si nous connectons les site a une application desktop
     * pour les echanger de donnee
     * @return string
     */
    public function toXML() : string
    {
        $refClass = new \ReflectionClass($this);
        $xml = '<entity className="'.$refClass->getShortName().'" namespace="'.$refClass->getNamespaceName().'">';
        
        /**
         * @var \ReflectionProperty[] $properties
         *
         */
        $properties = $refClass->getProperties();
        foreach ($properties as $properie) {
            $methodGET = 'get'.ucfirst($properie->getName());
            $methodIs = 'is'.ucfirst($properie->getName());
            
            $method = (method_exists($this, $methodGET))? ($methodGET) : (is_callable(array($this, $methodIs))? $methodIs : null);
            $type = $method!= null? (is_string($this->$method())? ("string") : (is_numeric($this->$method())? ("numeric") : (is_bool($this->$method())? ("boolean") : (is_array($this->$method())? "array" : null ) ) ) ) : null;
            
            if ($method == null || $this->$method() == null) {//elimination des proprietes qui n'ont pas de valeur
                continue;
            }
            
            if ($type == null && is_object($this->$method())) {
                $reflexion = new \ReflectionClass($this->$method());
                $type = $reflexion->getName();
            }
            
            $propNode = '<propertie name="'.$properie->getName().'" '.($type != null? ' type="'.$type.'"' : '');
            
            if (method_exists($this, $methodGET) && is_object($this->$methodGET())) {
                if (is_callable(array($this->$methodGET(), 'toXML'))) {//Si c'est un entity on empile sa methode de generation du XML
                    $propNode .= '> ';
                    $propNode .= $this->$methodGET()->toXML();
                    $propNode .= ' </propertie>';
                }elseif ($this->$methodGET() instanceof \DateTime){
                    $propNode .= ' value="'.$this->$methodGET()->format('d/m/Y à H\h:i').'"/>';
                }else{
                    $propNode .= ' value=""/>';
                }
            }elseif (method_exists($this, $methodGET) && is_array($this->$methodGET())){//Pour une collection des donnee
                $propNode .= '><list>';
                foreach ($this->$methodGET() as $value) {
                    $reflexion = new \ReflectionClass($value);
                    $propNode .= '<item type="'.$reflexion->getName().'">';
                    if (is_object($value)) {//Pour les objets
                        if (is_callable(array($value, 'toXML'))) {//Si c'est un entity on empile sa methode de generation du XML
                            $propNode .= $value->toXML();
                        }elseif ($this->$methodGET() instanceof \DateTime){//Pour les date
                            $propNode .= $this->$methodGET()->format('d/m/Y à H\h:i');
                        }
                    }else{//Pour une valeur simple
                        $propNode .= $value;
                    }
                    $propNode .= '</item>';
                }
                $propNode .= '</list></propertie>';
                
            }elseif (is_callable(array($this, $methodIs))) {
                $propNode .= ' value = "'.($this->$methodIs()==true? 'true':'false').'"/>';
            }else{//Si nom on recupere sa valeur
                $propNode .= ' value = "'.$this->$methodGET().'"/>';
            }
            $xml .= $propNode;
        }
        $xml .= '</entity>';
        return $xml;
    }
}

