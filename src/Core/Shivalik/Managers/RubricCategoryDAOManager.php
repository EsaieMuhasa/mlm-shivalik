<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\RubricCategory;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 * interface de communication de la table 
 * de classification des rubrique budgetaire
 */
interface RubricCategoryDAOManager extends DAOInterface {

    /**
     * verifie s'il y a aumon une categorie qui est ou n'est pas parainable
     *
     * @param boolean $ownable
     * @return boolean
     * @throws DAOException si une erreur surviens lors de la communication avec le SBGD
     */
    public function checkOwnable(bool $ownable = true) : bool;

    /**
     * selection les occurences qui sont ou ne sont pas parainable
     *
     * @param boolean $ownable
     * @return RubricCategory[]
     * @throws DAOException si une erreur surviens dans  le procesuce de communication avec le SGBD
     * soit aucun resultat n'est revoyer par la requette de selection
     */
    public function findOwnable (bool $ownable = true) : array;
}