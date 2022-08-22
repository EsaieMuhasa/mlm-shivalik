<?php

namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOInterface;
use Core\Shivalik\Entities\RequestVirtualMoney;

/**
 * @author Esaie Muhasa
 */
interface RequestVirtualMoneyDAOManager extends DAOInterface {

    /**
	 * y-a-il une demande de monais virtuel pour l'office en parametre??
	 * @param int $officeId
	 * @return boolean
	 */
	public function checkByOffice (int $officeId) : bool;
	
	/**
	 * Renvoie toutes les requettes d'une office
	 * @param int $officeId
	 * @return RequestVirtualMoney[]
	 */
	public function findByOffice (int $officeId);
	
	/**
	 * y-a-il une demande en attente???
	 * @param int $officeId
	 * @return bool
	 */
	public function checkWaiting (?int $officeId = null) : bool;
	
	/**
	 * Renvoie les demandes ene attente
	 * @param int $officeId
	 * @return RequestVirtualMoney[]
	 */
	public function findWaiting (?int $officeId = null);

	/**
     * y-a-il aumoin un rapport/requete dans cette intervalle???
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $officeId
     * @return boolean
     */
    public function checkRequestedInInterval (\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null) : bool;
    
    /**
     * Renvoie le rapport/requeste dans l'intervale en parametre
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $officeId
     * @return RequestVirtualMoney[]
     */
    public function findRequestedInInterval (\DateTime $dateMin, \DateTime $dateMax, ?int $officeId = null) : array;
}

