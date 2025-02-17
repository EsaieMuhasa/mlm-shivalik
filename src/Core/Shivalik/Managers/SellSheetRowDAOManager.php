<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\MonthlyOrder;
use Core\Shivalik\Entities\SellSheetRow;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 * @author Esaie Muhasa
 */
interface SellSheetRowDAOManager extends DAOInterface {

    /**
     * comptage de operations deja faite par un membre
     *
     * @param int $memberId
     * @return int
     * @throws DAOException
     */
    public function countByMember(int $memberId) : int;

    /**
     * verification de l'existance de operaction qui font reference au membre en premier parametre.
     * le dexieme parametre joue un role dans la clause limit de la requette sql. il s'ajit du nombre d'occurence
     * a sauter avant de faire la verification
     *
     * @param int $memberId
     * @param int $offset
     * @return boolean
     * @throws DAOException
     */
    public function checkByMember(int $memberId, int $offset = 0) : bool;

    /**
     * selection des operations de vente sur la fiche d'un membre.
     *
     * @param int $memberId
     * @param int|null $limit
     * @param int $offset
     * @return SellSheetRow[]
     * @throws DAOException s'il y a une erreur lors de la communication avec la base de donnee
     * soit aucun resultat n'est renvoyer par la requette de selection
     */
    public function findByMember(int $memberId, ?int $limit = null, int $offset = 0) : array;

    /**
     * verfie s'il y a aumoin une operation pour l'achat des produits pour le mois en parametre
     * @param int $monthlyOrder l'identifiant du mois est aumoin referencer
     * @return bool
     * @throws DAOException  si une erreur surveies dans le processuce de communication avec la base de donnee
     */
    public function checkByMonthlyOrder (int $monthlyOrder) : bool;
    
    /**
     * selection de tout les operations qui reference le mois en parametre
     * @param int $monthlyOrder l'identifiant du mois referencer
     * @return SellSheetRow[]
     * @throws DAOException si une erreur surviens dans le processuce de communication avec la base de donnee
     * soit aucun result n'est revoyer par la requette de selection
     */
    public function findByMonthlyOrder (int $monthlyOrder) : array;
    
    /**
     * comptage des operations qui pointe vers le monthlyOrder
     * @param int $monthlyOrder
     * @return int le nombre d'occurence qui font reference au monthlyOrder en parametre
     * @throws DAOException si une erreur surviens dans le processuce de communication avec la base de donnee
     */
    public function countByMonthlyOrders (array $monthlyOrders) : int;

    /**
     * verification des operations qui font refence au monthlyOrder dont l'ID est en parmatre
     * @param int[]|MonthlyOrder[] $monthlyOrders collection des IDs ou de monthlyOrder
     * @param int $offset le nombre d'occurence a sauter dans le selection (dans la clause LIMIT du SQL)
     * @return bool 
     */
    public function checkByMonthlyOrders (array $monthlyOrders, int $offset = 0) : bool;
    
    /**
     * selection des operations qui font reference aus monthlyOrders en premier parametre
     * @param int[]|MonthlyOrder[] $monthlyOrders
     * @param int $limit
     * @param int $offset
     * @return SellSheetRow[]
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD
     */
    public function findByMonthlyOrders (array $monthlyOrders, ?int $limit = null, int $offset = 0) : array;

}
