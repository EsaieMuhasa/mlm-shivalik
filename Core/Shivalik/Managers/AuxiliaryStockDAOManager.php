<?php
namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOException;
use Core\Shivalik\Entities\AuxiliaryStock;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface AuxiliaryStockDAOManager extends StockDAOManager
{
    /**
     * verifie s'il y a aumoin un stock conforme a la valeur de $emty pour lintervale de selection , pour l'office en premier parametre
     * @tutorial Pour plus d'explication sur le parametre $empty => consulter la doc de la la methode checkByProduct() de l'interface StockDAOManager
     * @param int $officeId
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException, dans le cas où il y a une erreur lors de la communication avec le SGBD
     */
    public function checkByOffice (int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * Selection de tour les stocks auxiliaire de l'office en premier parametre, conforme  a la valeur de $empty
     * @tutorial Pour plus d'explication sur le parametre $empty => consulter la doc de la methode checkByProduct() de l'interface StockDAOManager
     * @param int $officeId
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return AuxiliaryStock[]
     * @throws DAOException dans le cas où il ya une erreur lors d ela communiation avec le SGBD,  ou aucun resultat n'a ete retourner par la requette
     * de selection
     */
    public function findByOffice (int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * comptage de stock de l'office en premier parametre
     * @tutorial Pour plus d'explication sur le parametre $empty => consulter la doc de la methode checkByProduct() de l'interface StockDAOManager
     * @param int $officeId
     * @param bool $empty
     * @return int
     * @throws DAOException s'il y a erreur lors dfe la communication avec le SGBD
     */
    public function countByOffice (int $officeId, ?bool $empty = null) : int;
    
    /**
     * verification des stocks dans un office, pour un produit specifique
     * @tutorial Pour plus d'explication sur le parametre $empty => consulter la doc de la methode checkByProduct() de l'interface StockDAOManager
     * @param int $productId
     * @param int $officeId
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException s'il ya erreur lors dela communicationavec le SGBD
     */
    public function checkByProductInOffice (int $productId, int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * selection des stocks des d'un produit, pour l'office en deuxieme parametre
     * @tutorial Pour plus d'explication sur le parametre $empty => consulter la doc de la methode checkByProduct() de l'interface StockDAOManager
     * @param int $productId
     * @param int $officeId
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return AuxiliaryStock[]
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD ou aucun resultat n'est returner par la
     * requette de selection
     */
    public function findByProductInOffice (int $productId, int $officeId, ?bool $empty = null, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * Comptage des stock d'un produit,dans le l'office en deuxieme parametre
     * @tutorial Pour plus d'explication sur le parametre $empty => consulter la doc de la methode checkByProduct() de l'interface StockDAOManager
     * @param int $productId
     * @param int $officeId
     * @param bool $empty
     * @return int
     * @throws DAOException dans le cas où il y a erreur lors de la communication avec le SGBD
     */
    public function countByProductInOffice (int $productId, int $officeId, ?bool $empty = null) : int;
    
    /**
     * verification des stocks auxilitraire pour les stock principale en premier parametre.
     * Pour plus d'info sur le parametre $empty, confert la methode checkByProduct de l'interface StockDAOManager
     * @param int $parentId
     * @param int $officeId
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException
     */
    public function checkByParent (int $parentId, ?int $officeId = null, ?bool $empty = null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * selection des stock auxiliaire lier au stock pricipale en premier parametre
     * @param int $parentId
     * @param int $officeId
     * @param bool $empty
     * @param int $limit
     * @param int $offset
     * @return AuxiliaryStock[]
     */
    public function findByParent (int $parentId, ?int $officeId = null, ?bool $empty = null, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * comptage des stocks auxiliaire du stock principale en premier parametre
     * @param int $parentId
     * @param int $officeId
     * @param bool $empty
     * @return bool
     * @throws DAOException s'il y a erreur lose de la communication avec le SGBD
     */
    public function countByParent (int $parentId, ?int $officeId = null, ?bool $empty = null) : bool;
    
}

