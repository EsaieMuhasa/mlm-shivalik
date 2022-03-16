<?php

namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\VirtualMoney;
use PHPBackend\Dao\DAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
interface VirtualMoneyDAOManager extends DAOInterface {
	
	/**
	 * revoie tout le monais virtuel d'un office
	 * @param int $officeId
	 * @return VirtualMoney[]
	 */
	public function findByOffice (int $officeId);
	
	/**
	 * @param int $officeId
	 * @return bool
	 */
	public function checkByOffice (int $officeId) : bool;
	
	/**
	 * @param int $requestId
	 * @return VirtualMoney
	 */
	public function findByRequest (int $requestId) : VirtualMoney;	
	
	/**
	 * La requette a-t-l aumoin une reponse
	 * @param int $requestId
	 * @return bool
	 */
	public function checkByRequest (int $requestId) : bool;

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
	 * @return VirtualMoney[]
	 */
	public function findCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array ;

}

