<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOInterface;
use Core\Shivalik\Entities\ProductOrdered;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface ProductOrderedDAOManager extends DAOInterface {
    /**
     * Renvoie la liste de produit sur une commande
     * @param int $commandId
     * @return ProductOrdered[]
     * @throws DAOException
     */
    public function findByCommand (int $commandId) : array;
    
    /**
     * Compte le nombre d'element sur une commande
     * @param int $commandId
     * @return int
     * @throws DAOException
     */
    public function countByCommand(int $commandId) : int;
    
    /**
     * Verifie s'il y a aumoin un produit sur une commande
     * @param int $commandId
     * @return bool
     * @throws DAOException
     */
    public function checkByCommand (int $commandId) : bool;
    
    /**
     * Renvoie le commandes concernant un produit
     * @param int $productId
     * @param int $limit
     * @param int $offset
     * @return ProductOrdered[]
     * @throws DAOException
     */
    public function findByProduct (int $productId, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * Compte le nombre de commandes concernant un produit
     * @param int $productId
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException
     */
    public function checkByProduct (int $productId, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * compte le nombre de commande pour un produit
     * @param int $productId
     * @return int
     * @author DAOException
     */
    public function countByProduct(int $productId) : int;
}

