<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Withdrawal;
use PHPBackend\Dao\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
interface WithdrawalDAOManager extends OperationDAOManager
{

    
    /**
     * validation du retrait d'argent par un administrateur d'un office
     * @param int $d
     * @param int $adminId
     */
    public function validate (int $id, int $adminId) : void ;
    
    
    /**
     * Recuperaion des element d'un raport
     * @param int $raportId
     * @param int $limit
     * @param int $offset
     * @return Withdrawal[]
     * @throws DAOException
     */
    public function findByRapport(int $raportId, ?int $limit = null, int $offset = 0): array;
    
    /**
     * comptage des opperations qui ont ete envoyer dans un rappot
     * @param int $rapportId
     * @return int
     */
    public function countByRapport (int $rapportId) : int ;
    
    /**
     * verifie si l'office as des operations qui y ont transiter
     * @param int $officeId
     * @param bool $state
     * @param bool $sended
     * @return bool
     */
    public  function checkByOffice (int $officeId, ?bool $state = false, ?bool $sended=null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * renvoie les operations qui ont transiter par l'office en premier parametre
     * @param int $officeId
     * @param bool $state
     * @param bool $sended
     * @param int $limit
     * @param int $offset
     * @return Withdrawal[]
     */
    public function findByOffice (int $officeId, ?bool $state = false, ?bool $sended=null, ?int $limit = null, int $offset = 0);

    
    /**
     * verification de l'historique des operations effectuer pas un office
     * @param int $officeId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function checkCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool ;
    
    /**
     * recuperation des l'historique des operations effectuer par un office
     * @param int $officeId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return Withdrawal[]
     */
    public function findCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array;
    
    
    /**
     * Redirection d'une demande de matching
     * @param Withdrawal $with
     * @throws DAOException
     */
    public function redirect (Withdrawal $with) : void;

}
