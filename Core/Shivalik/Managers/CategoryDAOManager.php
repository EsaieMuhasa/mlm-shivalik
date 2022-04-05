<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Category;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface CategoryDAOManager extends DAOInterface
{
    /**
     * Vefification de l'existance du titre d'une categie dans la BDD
     * <br/> si $id != null, alors la verificatione est faite en faisant abastraction a l'occurence proprietaire du dit ID
     * @param string $title
     * @param int $id
     * @return bool
     * @throws DAOException s'il y a une erreur lors de la communication avec le SGBD
     */
    public function checkByTitle (string $title, ?int $id = null) : bool;
    
    /**
     * Renvoie le categorie dont le titre est en parametre
     * @param string $title
     * @return Category
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD, ou s'il n'y a aucun resultat
     */
    public function findByTitle (string $title) : Category;
}

