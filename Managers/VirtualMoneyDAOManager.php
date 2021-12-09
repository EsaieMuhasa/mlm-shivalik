<?php

namespace Managers;

use Library\AbstractDAOManager;
use Entities\VirtualMoney;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class VirtualMoneyDAOManager extends AbstractDAOManager {
	
	/**
	 * revoie tout le monais virtuel d'un office
	 * @param int $officeId
	 * @return VirtualMoney[]
	 */
	public function forOffice (int $officeId) {
		return $this->pdo_fromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'office', $officeId);
	}
	
	/**
	 * @param int $officeId
	 * @return bool
	 */
	public function hasVirtualMoney (int $officeId) : bool {
		return $this->columnValueExist('office', $officeId);
	}
	
	/**
	 * @param int $requestId
	 * @return VirtualMoney
	 */
	public function getResponse (int $requestId) {
		return $this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'request', $requestId);
	}
	
	
	/**
	 * La requette a-t-l aumoin une reponse
	 * @param int $requestId
	 * @return bool
	 */
	public function hasResponse (int $requestId) : bool {
		return $this->columnValueExist('request', $requestId);
	}
	
	
}

