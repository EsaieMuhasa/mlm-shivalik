<?php
namespace PHPBackend\Graphics\ChartJS;

use PHPBackend\Serialisation\JSONSerialize;

/**
 *
 * @author Esaie MHS
 *        
 */
class Chart
{
    /**
     * la configuration du chart
     * @var ChartConfig
     */
    private $config;
    
    use JSONSerialize;
    
    /**
     * constructeur d'initialisation
     * @param ChartConfig $config
     */
    public function __construct(ChartConfig $config) {
        $this->config = $config;
    }
    
    /**
     * @return \PHPBackend\Graphics\ChartJS\ChartConfig
     */
    public function getConfig () : ChartConfig
    {
        return $this->config;
    }

}

