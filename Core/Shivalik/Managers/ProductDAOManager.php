<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Product;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface ProductDAOManager extends DAOInterface
{
    
    /**
     * verification du nom d'un produit dans la BDD
     * @param string $name
     * @param int $id
     * @return bool
     * @throws DAOException
     */
    public function checkByName (string $name, ?int $id = null) : bool ;
    
    /**
     * renvoie le produit dont le nom est en parametre
     * @param string $name
     * @return Product
     * @throws DAOException
     */
    public function findByName (string $name) : Product;
    
    /**
     * mis en jour de la photo d'un produit
     * @param string $path
     * @param int $id
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public function updatePicture (string $path, int $id) : void;
}

