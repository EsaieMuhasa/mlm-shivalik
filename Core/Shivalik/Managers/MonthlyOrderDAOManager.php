<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\MonthlyOrder;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface MonthlyOrderDAOManager extends OperationDAOManager {
    
    /**
     * dispatch all bonus of current month
     * @return void
     * @throws DAOException dans le cas ou il y a une erreur dans le processuce de transmission du bonue de re-achat
     */
    public function dispatchPurchaseBonus () : void;
    
    /**
     * Comptage des operations enregister au compte du mois dont les coordonnees sont en prametre
     * @param int $month index du mois (une valeur numerique entier compris entre 1 et 12)
     * @param int $yer une valeur numerique entier superrieur a 1970
     * @param bool $status
     * @return int
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD
     */
    public function countByMonth (?int $month = null, ?int $yer=null, ?bool $status = null) : int;
    
    /**
     * verification des commands mensuel.
     * dans le cas où aucun parametre n'est au rendez-vous, alors le commandes renvoyer serons ceux du mois courant
     * @param int $month
     * @param int $year
     * @param bool $status
     * @param int $offset
     * @return bool
     * @throws DAOException s'il y a erreur lors de la communication avec la bdd
     */
    public function checkByMonth (?int $month = null, ?int $year = null, ?bool $status = null, int $offset = 0) : bool;
    
    /**
     * renvoie la collection des comptes qui on deja effectuer aumoin une commande pour le  moi en  parametre.
     * dans le cas où aucun parametre n'est au rendez-vous, le mois courant est celui pris en compte.
     * @param int $month index du mois (une valeur numerique entier entre 1 et 12)
     * @param int $year index de l'annee, une valeur numerique entiere superieur a 1970
     * @param bool $status etat de la commande. (deja invalider ou en attante de dispatching)
     * @return MonthlyOrder[]
     * @throws DAOException s'il y a erreur lors de la communication avec le SGBD ou aucun resultat ne correspond
     * a la requette de selection
     */
    public function findByMonth (?int $month = null, ?int $year = null, ?bool $status = null, ?int $limit = null, int $offset = 0) : array;
    
    /**
     * Check membre where id is first param in this function
     * @param int $memberId membre id in database
     * @param int $month index of month (in 1 et 12 interval)
     * @param int $year than 1970
     * @return bool
     * @throws DAOException:: when a error occured in process
     */
    public function checkByMemberOfMonth (int $memberId, ?int $month = null, ?int $year = null) : bool;
    
    /**
     * renvoie le compte mensuel de commandes du membre
     * @param int $memberId
     * @param int $month
     * @param int $year
     * @return MonthlyOrder
     */
    public function findByMemberOfMonth (int $memberId, ?int $month = null, ?int $year = null) : MonthlyOrder;
}

