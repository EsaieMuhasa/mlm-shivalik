<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOInterface;
use Core\Shivalik\Entities\Stock;
use PHPBackend\Dao\DAOException;

/**
 * @author Esaie MUHASA     
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
     * Chargement de tout les stocks (stock et tout les opratoions deja faite dans chaque stock)
     * @param int $limit
     * @param int $offset
     * @return Stock[]
     * @throws DAOException s'il y a erreur lors dela communication ave cle SGBD, 
     * ou impossible de determier le resultat a retourner
     */
    public function loadAll (?int $limit = null, int $offset = 0) : array;

    
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
    public function checkByProduct (int $productId, ?bool $empty = false, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * Comptage des stocks d'un produit
     * @param int $productId
     * @param bool $empty
     * @return int
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public function countByProduct (int $productId, ?bool $empty = false) : int;
    
    /**
     * recuperation des stocks du'un produit
     * @param int $productId
     * @param bool $empty : confert le parametre $empty de la method checkByProduct() : bool
     * @param int $limit
     * @param int $offset
     * @return Stock[]
     * @throws DAOException s'il n'y a aucun resultat, ou s'il y a erreur lors de la communication avec le SGBD
     */
    public function findByProduct (int $productId, ?bool $empty = false, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * Chargement des stocks d'un produit
     * @param int $productId
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return Stock[]
     */
    public function loadByProduct (int $productId, ?bool $empty = null, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * verification des stock, en fonction de leurs etat (vide, ou pas)
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException s'il y a erreur los de la communication avec le SGBD
     */
    public function checkByStatus (bool $empty = false, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * recuperation des stocks. filtrage en fonction de leurs etat
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return Stock[]
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD, ou aucun resultat lors de la selection
     */
    public function findByStatus (bool $empty = false, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * Chargement des tout le compte ayant pour status
     * @param boolean $empty
     * @param int $limit
     * @param int $offset
     * @return Stock[]
     */
    public function loadByStatus ($empty = false, ?int $limit = null, int $offset = 0) : array;
}

