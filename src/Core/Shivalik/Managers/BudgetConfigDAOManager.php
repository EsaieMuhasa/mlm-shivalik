<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\BudgetConfig;
use PHPBackend\Dao\DAOException;
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
     * @throws  DAOException si une erreur surviens lors de la communication avec le SGBD ou aucun resultat n''est renvoyer par 
     * la requette de selection
     */
    public function findAvailable() : BudgetConfig;

    /**
     * verification de l'existance d'un point de repere (un configuration de repartition du budget)
     * active (qui est encore d'actualite)
     *
     * @return boolean
     * @throws DAOException si une erreur surviens dans le processuce de communication avec le sgbd
     */
    public function checkAvailable() : bool;

}