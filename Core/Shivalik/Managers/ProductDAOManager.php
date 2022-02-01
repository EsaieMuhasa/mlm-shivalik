<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DefaultDAOInterface;
use Core\Shivalik\Entities\Product;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
abstract class ProductDAOManager extends DefaultDAOInterface
{
    
    /**
     * verification du nom d'un produit dans la BDD
     * @param string $name
     * @param int $id
     * @return bool
     * @throws DAOException
     */
    public function checkByName (string $name, ?int $id = null) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
    
    /**
     * renvoie le produit dont le nom est en parametre
     * @param string $name
     * @return Product
     * @throws DAOException
     */
    public function findByName (string $name) : Product {
        return  $this->findByColumnName('name', $name);
    }
    
    /**
     * mis en jour de la photo d'un produit
     * @param string $path
     * @param int $id
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public abstract function updatePicture (string $path, int $id) : void;
}

