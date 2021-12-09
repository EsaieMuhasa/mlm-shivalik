<?php

namespace Managers;

use Library\AbstractDAOManager;
use Entities\RequestVirtualMoney;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class RequestVirtualMoneyDAOManager extends AbstractDAOManager {
	
	/**
	 * @var OfficeDAOManager
	 */
	protected $officeDAOManager;
	
	/**
	 * @var VirtualMoneyDAOManager
	 */
	protected $virtualMoneyDAOManager;
	
	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractDAOManager::getForId()
	 * @return RequestVirtualMoney
	 */
	public function getForId(int $id, bool $forward = true) {
		$request = parent::getForId ($id, $forward);
		$request->setOffice($this->officeDAOManager->getForId($request->office->id, false));
		return $request;
	}
	
	/**
	 * y-a-il une demande de monais virtuel pour l'office en parametre??
	 * @param int $officeId
	 * @return boolean
	 */
	public function hasRequest (int $officeId)  {
		return  $this->pdo_columnValueExistInTable($this->getTableName(), 'office', $officeId);
	}
	
	/**
	 * y-a-il une demande en attente???
	 * @param int $officeId
	 * @return bool
	 */
	public abstract function hasWaiting (?int $officeId = null) : bool;
	
	/**
	 * REvoie les demandes ene attente
	 * @param int $officeId
	 * @return RequestVirtualMoney[]
	 */
	public abstract function getWaiting (?int $officeId = null);
	
	/**
	 * Revoie toutes les requettes d'une office
	 * @param int $officeId
	 * @return RequestVirtualMoney[]
	 */
	public function getOfficeRequest (int $officeId) {
		return $this->pdo_fromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'office', $officeId);
	}	
}

