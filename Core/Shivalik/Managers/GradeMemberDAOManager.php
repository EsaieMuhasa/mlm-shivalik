<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\GradeMember;
use PHPBackend\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class GradeMemberDAOManager extends DefaultDAOInterface
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
     * revoie l'actuel packet du membre dont l'id est en paramtres
     * (le packet envoyer est celui qui est actuelement activer)
     * @param int $memberId
     * @return GradeMember
     * @throws DAOException
     */
    public abstract function findCurrentByMember (int $memberId) : GradeMember ;
    
    /**
     * renveoie la demande de mise en jour du packet d'un utilisateur
     * @param int $memberId
     * @return GradeMember
     */
    public abstract function findRequestedByMember (int $memberId) : GradeMember;
    
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
    public abstract function checkByOffice (?int $officeId, ?bool $upgrade = null, ?bool $virtual = null) : bool;
    
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
    public abstract function findByOffice (?int $officeId, ?bool $upgrade = null, ?bool $virtual = null);
    
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
     * @throws DAOException s'il y a erreur lors de la communication avec la BDD ou aucune operations
     */
    public function findUnpaid (?int $officeId) : array {
        return $this->findOperations($officeId, null, false);
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
    public abstract function findDebts (?int $virtualId = null);
    
    
    /**
     * Renvoie la collection des packets en attente d'activation
     * @return GradeMember[]
     * @throws DAOException
     */
    public abstract function findAllRequest ();
    
    /**
     * Verifie s'il y a des packets en attente d'activation.
     * lorsque les packet sont en attente d'activation, les PV et l'argent n'est pas encore dispatcher
     * @return bool
     */
    public abstract function checkRequest () : bool;
    
    /**
     * @param int $memberId
     * @return bool
     */
    public abstract function checkCurrentByMember (int $memberId) : bool;
    
    /**
     * @param int $memberId
     * @return bool
     */
    public abstract function checkRequestedByMember (int $memberId) : bool;
    
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
     * c lors de l'appel a cette methode que les le bonus sont repartie
     * @param GradeMember $gm
     * @throws DAOException s'il y a erreur lors du partage des bonus
     */
    public abstract function enable (GradeMember $gm) : void ;
    
    
    /**
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws DAOException
     */
    public abstract function checkUpgradeHistory(\DateTime $dateMin, \DateTime $dateMax = null, ?int $officeId=null, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * comptage de upgrade de comptes
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $officeId
     * @return int
     * @throws DAOException
     */
    public abstract function countUpgradeHistory (\DateTime $dateMin, \DateTime $dateMax = null, ?int $officeId=null) : int;
    
    /**
     * reguperation de l'historique pour Upgrade
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return GradeMember[]
     */
    public abstract function findUpgradeHistory(\DateTime $dateMin, \DateTime $dateMax = null, ?int $officeId=null, ?int $limit = null, int $offset = 0);
    
    /**
     * @param GradeMember|int $gradeMember
     * @return GradeMember
     */
    public function load ($gradeMember) : GradeMember {
        $gm = ($gradeMember instanceof GradeMember)? $gradeMember : $this->findById($gradeMember);
        $gm->setOffice($this->officeDAOManager->findById($gm->getOffice()->getId(), false));
        $gm->setMember($this->memberDAOManager->findById($gm->getMember()->getId(), false));
        $gm->setGrade($this->gradeDAOManager->findById($gm->getGrade()->getId(), false));
        if ($gm->getOld() != null) {
            $gm->setOld($this->findById($gm->getOld()->getId(), false));
        }
        return $gm;
    }
    
}

