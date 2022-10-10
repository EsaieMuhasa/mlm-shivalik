<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 * Interface de communication avec la table qui sauvegarde les sous-repartition 
 * de la repartition globale du budget
 */
interface SubConfigElementDAOManager extends DAOInterface {

    /**
     * renvoie les repartitions de d'un element du budget
     *
     * @param int $elementId
     * @return SubConfigElement[]
     * @throws DAOException s'il y a une erreur lors de la communication avec la BDD,
     * soit aucun resultat n'est renvoyer par la requette de selection
     */
    public function findByElement (int $elementId) : array;

    /**
     * verifie si un element de la repartition globale du budget possede 
     * une sous repartition
     *
     * @param int $elementId
     * @return bool
     * @throws DAOException s'il y a une erreur lors de la communication avec la BDD
     */
    public function checkByElement (int $elementId) : bool;
}