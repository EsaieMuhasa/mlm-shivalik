<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DefaultDAOInterface;
use Core\Shivalik\Entities\Stock;

/**
 *
 * @author Esaie MUHASA
 *        
 */
abstract class StockDAOManager extends DefaultDAOInterface
{
    /**
     * verifie si le produit a un stock
     * @param int $productId
     * @param bool $empty: true si verification des stock vide, false si verification des stock non vide
     * null si verification du stock sans tenir compte de son etat
     * @return bool
     */
    public abstract function checkByProduct (int $productId, ?bool $empty = null) : bool;
    
    /**
     * recuperation des stocks du'un produit
     * @param int $productId
     * @param bool $empty : confert le parametre $empty de la method checkByProduct() : bool
     * @return array
     */
    public abstract function findByProduct (int $productId, ?bool $empty = null) : array;
    
    /**
     * verification des stock, en fonction de leurs etat
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public abstract function checkByStatus (bool $empty = false, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * recuperation des stocks. filtrage en fonction de leurs etat
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return Stock[]
     */
    public abstract function findByStatus (bool $empty = false, ?int $limit = null, int $offset = 0) : bool;
}

