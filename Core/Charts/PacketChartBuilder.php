<?php
namespace Core\Charts;

use Library\Graphics\ChartJS\Tools\EntityChartBuilder;
use Library\Config;
use Library\Graphics\ChartJS\ChartConfig;
use Library\Graphics\ChartJS\ChartData;
use Entities\Member;
use Library\Graphics\ChartJS\ChartDataset;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class PacketChartBuilder extends EntityChartBuilder
{

    /**
     * (non-PHPdoc)
     *
     * @see \Library\Graphics\ChartJS\Tools\EntityChartBuilder::doGenerate()
     */
    protected function doGenerate(?Config $config) : ChartConfig
    {
        /**
         * @var array $poits
         * @var Member $packet
         */
        $statistics = [];
        
        $data = new ChartData();
        foreach ($this->getElements() as $packet) {
            
            if (array_key_exists($packet->getPacket()->getGrade()->getName(), $statistics)) {
                ++$statistics[$packet->getPacket()->getGrade()->getName()];
            }else {
                $statistics[$packet->getPacket()->getGrade()->getName()] = 1;
            }
        }
        $dataset = new ChartDataset();
        $labels = [];
        foreach ($statistics as $label => $statistic) {
            $dataset->addData($statistic);
            $labels[] = $label;
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

