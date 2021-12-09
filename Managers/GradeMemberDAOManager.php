<?php
namespace Managers;

use Library\AbstractDAOManager;
use Entities\GradeMember;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class GradeMemberDAOManager extends AbstractDAOManager
{
	
	/**
	 * @var MemberDAOManager
	 */
	protected $memberDAOManager;
	
	/**
	 * @var GradeDAOManager
	 */
	protected $gradeDAOManager;
	
	/**
	 * @var GenerationDAOManager
	 */
	protected $generationDAOManager;
	
	/**
	 * @var OfficeDAOManager
	 */
	protected $officeDAOManager;
	
    /**
     * @param int $memberId
     * @return GradeMember
     * @throws DAOException
     */
    public abstract function getCurrent (int $memberId) : GradeMember ;
    
    /**
     * renveoie la demande de mise en jour du packet d'un utilisateur
     * @param int $memberId
     * @return GradeMember
     */
    public abstract function getRequested (int $memberId) : GradeMember;
    
    /**
     * L'office en parametre a-t-elle deja effectuee aumoin une operation???
     * Lors de la verification, si le parametre virtual veau:
     * <ul>
     * <li>null: alors la colonne de la reference des virtuel est omise dans la close WHERE</li>
     * <li>true: on recupere uniquement les operations dont la retocession a deja eu lieux</li>
     * <li>false: on recupere les operation dont la retrocession n'as pas enore leux lieux</li>
     * </ul>
     * @param int $officeId
     * @param bool $upgrade
     * @param bool $virtual 
     * @return bool
     */
    public abstract function hasOperation (?int $officeId, ?bool $upgrade = null, ?bool $virtual = null) : bool;
    
    /**
     * revoie une collection d'operation effectuer par un bureau
     * si le parametre virtual veau:
     * <ul>
     * <li>null: alors la colonne de la reference des virtuel est omise dans la close WHERE</li>
     * <li>true: on recupere uniquement les operations dont la retocession a deja eu lieux</li>
     * <li>false: on recupere les operation dont la retrocession n'as pas enore leux lieux</li>
     * </ul>
     * @param int $officeId
     * @param bool $upgrade
     * @param bool $virtual
     * @return GradeMember[]
     */
    public abstract function getOperations (?int $officeId, ?bool $upgrade = null, ?bool $virtual = null);
    
    /**
     * Verifie s'il a des operation dont la rertrocession n'a pas encore eu lieux
     * @param int $officeId
     * @return bool
     */
    public function hasUnpaid (?int $officeId) : bool {
        return $this->hasOperation($officeId, null, false);
    }
    
    /**
     * renvoie une collection des operations dont la retrocession n'as pas encore eu lieux
     * si l'officeId est omise, alors on verfie pour tout les offices dans le systeme
     * @param int $officeId
     * @return GradeMember[]
     */
    public function getUnpaid (?int $officeId) : array {
        return $this->hasOperation($officeId, null, false);
    }
    /**
     * y-a-il aumoin une operation pour le virtual en parametre??
     * @param int $virtualId
     * @return bool
     */
    public abstract function hasDebts (?int $virtualId = null) : bool ;
    
    /**
     * Revoie une collection d'operation en dettes 
     * donc les operation qui ont impacter sur le montant virtual
     * @param int $virtualId
     * @return GradeMember[]
     */
    public abstract function getDebts (?int $virtualId = null);
    
    
    /**
     * Renvoie la collection des packets en attente d'activation
     * @return GradeMember[]
     * @throws DAOException
     */
    public abstract function getAllRequest ();
    
    /**
     * Verifie s'il y a des packets en attente d'activation.
     * lorsque les packet sont en attente d'activation, les PV et l'argent n'est pas encore dispatcher
     * @return bool
     */
    public abstract function hasRequest () : bool;
    
    /**
     * @param int $memberId
     * @return bool
     */
    public abstract function hasCurrent (int $memberId) : bool;
    
    /**
     * @param int $memberId
     * @return bool
     */
    public abstract function hasRequested (int $memberId) : bool;
    
    /**
     * @param GradeMember $gm
     * @throws DAOException
     */
    public abstract function upgrade (GradeMember $gm) : void ;
    
    /**
     * comptage des operations d'apgrade de compte??
     * @param int $officeId
     * @return int
     */
    public abstract function countUpgrades (?int $officeId = null) : int ;
    
    /**
     * Activation de packet d'un utilisateur
     * 
     * @param GradeMember $gm
     */
    public abstract function enable (GradeMember $gm) : void ;
    
    
    /**
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public abstract function hasUpgradeHistory(\DateTime $dateMin, \DateTime $dateMax = null, array $filters = array(), $limit = - 1, $offset = - 1) : bool;
    
    /**
     * reguperation de l'historique pour Upgrade
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return GradeMember[]
     */
    public abstract function getUpgradeHistory(\DateTime $dateMin, \DateTime $dateMax = null, array $filters = array(), $limit = - 1, $offset = - 1);
}

