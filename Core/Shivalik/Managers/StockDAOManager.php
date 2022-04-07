<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOInterface;
use Core\Shivalik\Entities\Stock;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface StockDAOManager extends DAOInterface
{
    /**
     * Chargement complet d'un stock
     * @param Stock|int $stock
     * @return Stock
     * @throws DAOException s'il y a une erreur lors du chargement des donnees depuis la BDD
     */
    public function load ($stock) : Stock;
    
    /**
     * verifie si le produit a un stock
     * @param int $productId
     * @param bool $empty: true si verification des stock vide, false si verification des stock non vide
     * null si verification du stock sans tenir compte de son etat
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException s'il ya erreur lors de la communication avec le SGBD
     */
    public function checkByProduct (int $productId, ?bool $empty = null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * recuperation des stocks du'un produit
     * @param int $productId
     * @param bool $empty : confert le parametre $empty de la method checkByProduct() : bool
     * @param int $limit
     * @param int $offset
     * @return Stock[]
     * @throws DAOException s'il n'y a aucun resultat, ou s'il y a erreur lors de la communication avec le SGBD
     */
    public function findByProduct (int $productId, ?bool $empty = null, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * verification des stock, en fonction de leurs etat (vide, ou pas)
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function checkByStatus (bool $empty = false, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * recuperation des stocks. filtrage en fonction de leurs etat
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return Stock[]
     */
    public function findByStatus (bool $empty = false, ?int $limit = null, int $offset = 0) : array;
}

