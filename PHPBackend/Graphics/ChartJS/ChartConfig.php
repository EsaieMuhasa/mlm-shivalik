<?php
namespace PHPBackend\Graphics\ChartJS;

use PHPBackend\Serialisation\JSONSerialize;

/**
 *
 * @author Esaie MHS
 *        
 */
class ChartConfig
{
    const TYPE_LINE_CHART = 'line';
    const TYPE_PIE_CHART = 'pie';
    const TYPE_BAR_CHART = 'bar';
    
    private static $TYPE_CHARTS = ['line', 'pie', 'bar'];
    
    
    /**
     * Le type du graphique
     * @var string
     */
    private $type;
    
    /**
     * @var ChartData
     */
    private $data;
    
    use JSONSerialize;

    /**
     * constructeur d'initialisation de la configuration
     * @param ChartData $data
     * @param string $type
     */
    public function __construct(ChartData $data, string $type = self::TYPE_LINE_CHART)
    {
        $this->setData($data);
        $this->setType($type);
    }
    
    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return \PHPBackend\Graphics\ChartJS\ChartData
     */
    public function getData() : ChartData
    {
        return $this->data;
    }

    /**
     * @param string $type
     */
    public function setType(string $type) : void
    {
        foreach (self::$TYPE_CHARTS as $t) {
            if ($type == $t) {
                $this->type = $type;
                return;
            }
        }
        
        throw new ChartException("Type de graphique non prise en charge => {$type}");
    }

    /**
     * @param \PHPBackend\Graphics\ChartJS\ChartData $data
     */
    public function setData(ChartData $data) : void
    {
        $this->data = $data;
    }

}

