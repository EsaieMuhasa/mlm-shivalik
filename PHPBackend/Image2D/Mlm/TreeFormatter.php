<?php
namespace PHPBackend\Image2D\Mlm;

use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class TreeFormatter extends TreeBuilder
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';
    const FORMAT_HTML = 'html';
    
    private $formated;
    
    /**
     * (non-PHPdoc)
     *
     * @see \PHPBackend\Image2D\Mlm\TreeBuilder::getLevel()
     */
    public function getLevel() : int
    {
        throw new PHPBackendException("unsupported task");
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\TreeBuilder::process()
     */
    public function process(): void
    {
        throw new PHPBackendException("use the 'format()' method to perform formatting");
    }

    /**
     * @return string|NULL
     */
    public function format (string $format = self::FORMAT_JSON) : ?string{
        if ($this->getRoot() == null) {
            return null;
        }
        if ($format == self::FORMAT_JSON) {
            $root = $this->getRoot();
            $json = " {";
            $json .= "\"Id\":\"{$root->getId()}\",";
            $json .= "\"name\":\"{$root->getNodeName()} [{$root->matricule}]\"";
            $json .= ",\"icon\":\"{$root->getIcon()->getDefault()}\"";
            $json .= ",\"foot\":".($root->getFoot()==null? "null" : $root->getFoot());
            if ($root->hasChilds()) {
                $json .= ",\"childs\": [";
                foreach ($root->getChilds() as $key => $node) {
                    $json .= $this->formatChild($node, $format).(($key != (count($root->getChilds())-1)) ? "," : "");
                }
                $json .= "]";
            }
            
            $json .= "}";
            return $json;
        }elseif ($format == self::FORMAT_XML) {
            $root = $this->getRoot();
            $xml = "<node";
            $xml .= " name=\"{$root->getNodeName()}\"";
            $xml .= " icon=\"{$root->getIcon()->getDefault()}\"";
            if ($root->getFoot() != null) {
                $xml .= " foot=\"{$root->getFoot()}\"";
            }
            $xml .= ">";
            if ($root->hasChilds()) {
                $xml .= "<childs>";
                foreach ($root->getChilds() as $key => $node) {
                    $xml .= $this->formatChild($node, $format);
                }
                $xml .= "</childs>";
            }
            
            $xml .= "</node>";
            return $xml;
        }elseif ($format == self::FORMAT_HTML) {
            $root = $this->getRoot();
            $html = "<ul class=\"list list-group\">";
            $html .= "<li class=\"list-group-item\" style=\"overflow: auto;\">";
            $html .= "<img style=\"width: 25px; border-radius: 50%;\" src=\"/{$root->getIcon()->getDefault()}\"/>";
            $html .= "<strong> {$root->getNodeName()}</strong>";
            if ($root->hasChilds()) {
                $html .= "<ul >";
                foreach ($root->getChilds() as $key => $node) {
                    $html .= $this->formatChild($node, $format);
                }
                $html .= "</ul>";
            }
            $html .= "</li>";
            
            $html .= "</ul>";
         
            return $html;
        }
        throw new PHPBackendException("Format on suport");
    }
    
    /**
     * @param Node $node
     * @return string
     */
    private function formatChild ($node, string $format) : string {
        if ($format == self::FORMAT_JSON) {            
            $json = " {";
            $json .= "\"Id\":\"{$node->getId()}\",";
            $json .= "\"name\":\"{$node->getNodeName()}\"";
            $json .= ",\"icon\":\"{$node->getIcon()->getDefault()}\"";
            $json .= ",\"foot\":".($node->getFoot()==null? "null" : $node->getFoot());
            if ($node->hasChilds()) {
                $json .= ",\"childs\": [";
                foreach ($node->getChilds() as $key => $child) {
                    $json .= $this->formatChild($child, $format).(($key != (count($node->getChilds())-1)) ? "," : "");
                }
                $json .= "]";
            }
            
            $json .= "}";
            return $json;
        }else if($format == self::FORMAT_XML){
            $xml = "<node ";
            $xml .= "name=\"{$node->getNodeName()}\"";
            $xml .= " icon=\"{$node->getIcon()->getDefault()}\"";
            if ($node->getFoot() != null) {
                $xml .= " foot=\"{$node->getFoot()}\"";
            }
            $xml .= ">";
            if ($node->hasChilds()) {
                $xml .= "<childs>";
                foreach ($node->getChilds() as $key => $child) {
                    $xml .= $this->formatChild($child, $format);
                }
                $xml .= "</childs>";
            }
            
            $xml .= "</node>";
            return $xml;
        }else{
            $html = "<li> ";
            $html .= "<img style=\"width: 25px; border-radius: 50%;\" src=\"/{$node->getIcon()->getDefault()}\"/>";
            $html .= "<strong> {$node->getSponsor()->getId()} => {$node->getNodeName()}</strong>";
            if ($node->getFoot() != null) {
                $html .= " <span class=\"badge\">foot: {$node->getFoot()}, ID {$node->id}</span>";
            }
            if ($node->hasChilds()) {
                $html .= "<ul>";
                foreach ($node->getChilds() as $key => $child) {
                    $html .= $this->formatChild($child, $format);
                }
                $html .= "</ul>";
            }
            
            $html .= "</li>";
            return $html;
        }
    }
    
}

