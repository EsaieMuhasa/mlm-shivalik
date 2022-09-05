<?php
namespace PHPBackend\Graphics\ChartJS;

use PHPBackend\Serialisation\JSONSerialize;

/**
 *
 * @author Esaie MHS
 *        
 */
class ChartDataset
{

    /**
     * Collection des couleurs de remplissage
     * @var array
     */
    private $backgroundColor = [];
    
    
    /**
     * collection des couleurs de bordure
     * @var array
     */
    private $borderColor = [];
    
    /**
     * Epaisseur de la ligne du graphique
     * @var int
     */
    private $borderWidth;
    
    
    /**
     * @var boolean
     */
    private $fill;
    
    
    /**
     * la tension pour la manipulation des courbe de besier
     * @var float
     */
    private $tension;
    
    
    /**
     * Collection des poits de y,
     * soit une collection dobjet de tyle poits
     * @var number[]|ChartPoint[]
     */
    private $data = [];
    
    /**
     * la legende du graphique
     * @var string
     */
    private $label;
    
    
    /**
     * le parametre de parsage du graphique
     * c'est une collection associative
     * @var array
     */
    private $parsing = [];
    
    
    use JSONSerialize;
    

    /**
     * Construteur d'initialisation
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->borderWidth = 3;
        $this->tension = 0.25;
        $this->fill = false;
    }
    
    /**
     * @return array
     */
    public function getBackgroundColor() : array
    {
        return $this->backgroundColor;
    }

    /**
     * @return array
     */
    public function getBorderColor() : array
    {
        return $this->borderColor;
    }

    /**
     * @return number
     */
    public function getBorderWidth() : int
    {
        return $this->borderWidth;
    }

    /**
     * @return boolean
     */
    public function isFill() : bool
    {
        return $this->fill;
    }

    /**
     * @return number
     */
    public function getTension() : float
    {
        return $this->tension;
    }

    /**
     * @return mixed <multitype:number , multitype:\PHPBackend\Graphics\ChartJS\ChartPoint >
     */
    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getLabel() : ?string
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getParsing() : array
    {
        return $this->parsing;
    }

    /**
     * @param array $backgroundColor
     */
    public function setBackgroundColor($backgroundColor) : void
    {
        $this->backgroundColor = $backgroundColor;
    }

    /**
     * @param array $borderColor
     */
    public function setBorderColor($borderColor) : void
    {
        $this->borderColor = $borderColor;
    }

    /**
     * @param number $borderWidth
     */
    public function setBorderWidth($borderWidth) : void
    {
        $this->borderWidth = $borderWidth;
    }

    /**
     * @param boolean $fill
     */
    public function setFill($fill) : void
    {
        $this->fill = $fill;
    }

    /**
     * @param number $tension
     */
    public function setTension($tension) : void
    {
        $this->tension = $tension;
    }

    /**
     * @param Ambigous <multitype:number , multitype:\PHPBackend\Graphics\ChartJS\ChartPoint > $data
     */
    public function setData($data) : void
    {
        $this->data = $data;
    }
    
    /**
     * Ajout d'une coordonnee graphique
     * @param int|ChartPoint $value
     */
    public function addData ($value) : void {
        array_push($this->data, $value);
    }

    /**
     * @param string $label
     */
    public function setLabel($label) : void
    {
        $this->label = $label;
    }

    /**
     * @param array $parsing
     */
    public function setParsing($parsing) : void
    {
        $this->parsing = $parsing;
    }
    
    /**
     * Ajout d'un parametre de parsage du graphique
     * @param string $name
     * @param mixed $value
     */
    public function addParsingParam (string $name, $value)  : void {
        $this->parsing[$name] = $value;
    }

}

