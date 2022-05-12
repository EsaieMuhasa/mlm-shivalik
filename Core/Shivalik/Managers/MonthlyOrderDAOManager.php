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
     */
    public function dispatchPurchaseBonus () : void;
    
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

