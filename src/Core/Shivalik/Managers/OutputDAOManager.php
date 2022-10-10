<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Output;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 * interface de la table qui sauvegarde les operations 
 * de retrais de l'argent sur une rubrique budgetaire
 */
interface OutputDAOManager extends DAOInterface {

    /**
     * verifie s'il y a aumoin une operations qui fait reference au rubique dont l'id est en parametre
     *
     * @param int $rubricId
     * @return bool
     * @throws DAOException si une erreur surviens dans le processuce de communication avec le SGBD
     */
    public function checkByRubric (int $rubricId) : bool;

    /**
     * selection des tout les operations qui font reference 
     * a la rubrique budgetaire en parametre
     *
     * @param int $rubricId
     * @return Output[]
     */
    public function findByRubric (int $rubricId) : array;
}