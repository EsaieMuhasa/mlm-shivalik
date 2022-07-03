<?php

namespace Core\Shivalik\Managers;

use PHPBackend\Dao\DAOInterface;
use Core\Shivalik\Entities\RequestVirtualMoney;

/**
 *
 * @author Esaie MHS
 * @deprecated la depreciation de l'entite gerer par cette interface entraine carrement la depreciation de celle-ci 
 */
interface RequestVirtualMoneyDAOManager extends DAOInterface {

    /**
	 * y-a-il une demande de monais virtuel pour l'office en parametre??
	 * @param int $officeId
	 * @return boolean
	 */
	public function checkByOffice (int $officeId) ;
	
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
}

