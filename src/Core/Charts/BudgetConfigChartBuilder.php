<?php
namespace Core\Charts;

use Core\Shivalik\Entities\ConfigElement;
use PHPBackend\AppConfig;
use PHPBackend\Graphics\ChartJS\ChartConfig;
use PHPBackend\Graphics\ChartJS\ChartData;
use PHPBackend\Graphics\ChartJS\ChartDataset;
use PHPBackend\Graphics\ChartJS\Tools\EntityChartBuilder;

class BudgetConfigChartBuilder extends EntityChartBuilder {

    /**
     * {@inheritDoc}
     */
    protected function doGenerate(?AppConfig $config) : ChartConfig
    {

        $data = new ChartData();
        $dataset = new ChartDataset();
        $labels = [];
        foreach ($this->getElements() as $element) {
            $dataset->addData($element->getPercent());
            $labels[] = $element->getRubric()->getLabel();
        }
        
        $dataset->setBackgroundColor(["#EE5050EE", "#A0A0A0FF", "#0000EEFF", "#FF77A9FF", "#50AAEFFF", "#000000FF"]);
        //$dataset->setBorderColor(["#EE5050AA", "#A0A0A0AA", "#0000EEAA", "rgba(255, 99, 132, 0.6)", "rgba(54, 162, 235, 0.6)", "#000000AA"]);
        $dataset->setBorderColor(["#FFFFFF"]);
        $dataset->setBorderWidth(1);
        
        $dataset->addParsingParam('yAxisKey', 'y');
        
        $data->setLabels($labels);
        $data->addDataset($dataset);
        return new ChartConfig($data);
    }
}