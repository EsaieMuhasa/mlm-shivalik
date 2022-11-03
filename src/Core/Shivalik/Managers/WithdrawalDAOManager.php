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
    public function countByRapport (int $rapportId) : int;
    
    /** 
     * Comptage des operations deja effectuers dans un bureau (operations de cashouts)
     * @param int $officeId, identifiant de l'office
     * @param bool $state, status du cashout
     * @param bool $sended, est-il deja transmis a la hierarchie??
     * @return int
     * @throws DAOException s'il ya erreur lors de la communication avec la BDD
     */
    public function countByOffice (int $officeId, ?bool $state = false, ?bool $sended=null) : int;
    
    /**
     * verifie si l'office as des operations qui y ont transiter
     * @param int $officeId
     * @param bool $state : le matching doit-t-il etre deja servie
     * @param bool $sended :  le matching doit-t-il etre deja envoyer a l'adminitration centrale???
     * @return bool
     */
    public  function checkByOffice (int $officeId, ?bool $state = false, ?bool $sended=null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * renvoie les operations qui ont transiter par l'office en premier parametre
     * @param int $officeId
     * @param bool $state : le matching doit-t-il etre deja servie
     * @param bool $sended :  le matching doit-t-il etre deja envoyer a l'adminitration centrale???
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

    /**
     * renvoie la somme des montant demnager par les membres.
     * par defaut cette methode renvoie la somme total pour tout les offices.
     * 
     * en renseignant le parametre $officeKey, la somme est ceux des operations qui font reference a celle-ci
     *
     * @param integer|null $officeKey
     * @return float
     * @throws DAOException en cas d'erreur lors de la communication avec le SGBD
     */
    public function getSumAllRequested (?int $officeKey = null) : float;

    /**
     * renvoie la somme des montants deja servie pour tout le monde.
     * par defaut, la somme renvoyer est celle de tout les membres du systemes.
     * 
     * en specifiant le parametre $officeKey, la somme est faite uniquement pour les operations qui font reference
     * au dit office
     *
     * @param integer|null $officeKey
     * @return float
     */
    public function getSumAllServed (?int $officeKey = null) : float;

}
