<?php
namespace Library\Graphics\ChartJS;

use Library\Serialisation\JSONSerialize;

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
     * @return \Library\Graphics\ChartJS\ChartConfig
     */
    public function getConfig () : ChartConfig
    {
        return $this->config;
    }

}

