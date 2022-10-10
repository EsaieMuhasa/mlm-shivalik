<?php

namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\VirtualMoney;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DAOInterface;

/**
 * @author Esaie MUHASA <esaiemuhasa.dev@gmail.com>
 * interface de communication avec la table qui auvegarde le montant (considerer comme virtuel, terme courant 
 * des utilisateurs de shivalik)
 */
interface VirtualMoneyDAOManager extends DAOInterface {

	/**
	 * verifie s'il y a des operation qui font reference a la configuration en parametre.
	 * si $configId veau null, alors on verifie s'il y a de operation qui ne font reference au aucune 
	 * repartition budgetaire
	 *
	 * @param int|null $configId
	 * @return bool
	 * @throws DAOException
	 */
	public function checkInputByBudget(?int $configId = null) : bool;

	/**
	 * selection tout les operations qui font reference a la configuration du budget.
	 * si $configId = null, alors on selectionne les operations qui ne font reference au configs
	 *
	 * @param int|int $configId
	 * @return VirtualMoney[]
	 */
	public function findInputByBudget (?int $configId = null) : array; 
	
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

