<?php

namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\RequestVirtualMoney;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class RequestVirtualMoneyDAOManager extends DefaultDAOInterface {
	
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
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByColumnName()
     */
    public function findByColumnName(string $columnName, $value, bool $forward = true)
    {
        $request = parent::findByColumnName($columnName, $value, $forward);
		$request->setOffice($this->officeDAOManager->findById($request->office->id, false));        
        return $request;
    }

    /**
	 * y-a-il une demande de monais virtuel pour l'office en parametre??
	 * @param int $officeId
	 * @return boolean
	 */
	public function checkByOffice (int $officeId)  {
		return  $this->columnValueExist('office', $officeId);
	}
	
	/**
	 * Renvoie toutes les requettes d'une office
	 * @param int $officeId
	 * @return RequestVirtualMoney[]
	 */
	public function findByOffice (int $officeId) {
	    return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, array("office" => $officeId));
	}	
	
	/**
	 * y-a-il une demande en attente???
	 * @param int $officeId
	 * @return bool
	 */
	public abstract function checkWaiting (?int $officeId = null) : bool;
	
	/**
	 * Renvoie les demandes ene attente
	 * @param int $officeId
	 * @return RequestVirtualMoney[]
	 */
	public abstract function findWaiting (?int $officeId = null);
}

