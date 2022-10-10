<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\BudgetConfig;
use PHPBackend\Dao\DAOInterface;

/**
 * interface de communication avec la table qui sauvegarde les configurations 
 * de repartition de virtuel
 */
interface BudgetConfigDAOManager extends DAOInterface {

    /**
     * renvois la configuration actuelement au top
     *
     * @return BudgetConfig
     */
    public function findAvailable() : BudgetConfig;

}