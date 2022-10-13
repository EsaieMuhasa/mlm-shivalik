<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\BudgetRubric;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

interface BudgetRubricDAOManager extends DAOInterface {

    /**
     * revifie s'il y a une rubrique qui pointe vers le compte du membre dont l'ID est en parametre
     *
     * @param int $ownerKey
     * @return bool
     * @throws DAOException s'il y a une erreur lors de la communication avec le SGBD
     */
    public function checkOwnedByMember (int $ownerKey) : bool;

    /**
     * renvoie la collection des rubique qui pointe vers le compte du membre dont l'ID est en parametre
     *
     * @param int $ownerKey
     * @return BudgetRubric[]
     * @throws DAOException si une erreur surviens dans e processuce de communicationaveec la base de donnee
     */
    public function findOwnedByMember (int $ownerKey) : array;

    /**
     * renvoie le rubrique qui ne sont pa liee au compte personnel des membres d syndidat
     *
     * @return BudgetRubric[]
     * @throws DAOException si une erreur surviens dans le processuce de communication avec le SGBD
     * ou aucun resultat n'est renvoyer par la requette de selection
     */
    public function findUnowned () : array;

    /**
     * verifie s'il a aumoin une rubrique qui n'est binder au compte d'un membre du syndicat
     *
     * @return bool
     * @throws DAOException si une erreur surviens dans le processuce de communication avec le SGBD
     */
    public function checkUnowned () : bool;

    /**
     * verifie s'il ya de rubrique qui sont binder aux compte des membres
     *
     * @return boolean
     * @throws DAOException s une erreur seurviens dans le processuce de communication avec le serveur
     */
    public function checkOwned () : bool;

    /**
     * collecte tout les rubirques qui sont liee au compte des membres du syndicat
     *
     * @return BudgetRubric[]
     * @throws DAOException si une erreur surviens dans le processuce de selection,
     *  soit aucun resultat n'est renvoyer par la requette de selection
     */
    public function findOwned () : array;
    
}