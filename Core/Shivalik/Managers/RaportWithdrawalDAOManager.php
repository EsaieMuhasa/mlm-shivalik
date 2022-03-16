<?php
namespace Core\Shivalik\Managers;


use PHPBackend\Dao\DAOInterface;
use Core\Shivalik\Entities\RaportWithdrawal;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface  RaportWithdrawalDAOManager extends DAOInterface
{
    /**
     * Peut-ont envoyer le rapport
     * @param int $officeId
     * @return bool
     */
    public function canSendRaport(int $officeId) : bool;
    
    /**
     * y-a-il aumoin un rapport dans cette intervalle???
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $officeId
     * @return boolean
     */
    public function checkRaportInInterval (\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null) : bool;
    
    /**
     * Renvoie le rapport dans l'intervale en parametre
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $officeId
     * @return RaportWithdrawal[]
     */
    public function findRaportInInterval (\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null) : array;
    
    /**
     * y-a-il aumon un raport pour le bureau en parametre????
     * @param int $officeId
     * @return bool
     */
    public function checkByOffice (int $officeId) : bool;
    
    
    /**
     * renvoie les rapports deja envoyer au nom d'un bureau
     * @param int $officeId
     * @param int $limit
     * @param int $offset
     * @return RaportWithdrawal[]
     */
    public function findByOffice(int $officeId, ?int $limit = null, int $offset = 0) : array;
}

