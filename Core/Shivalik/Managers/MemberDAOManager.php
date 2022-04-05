<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Member;
use PHPBackend\Dao\DAOException;

/**
 * @author Esaie MHS
 */
interface MemberDAOManager extends UserDAOManager
{
	
	/**
	 * load all information for user account
	 * @param Member|int $member 
	 * @param bool $calcul
	 * @return Account
	 */
	public function loadAccount ($member, bool $calcul = true) : Account;
	
	/**
	 * Insersion d'un nouveau compte au dessu d'un autre compte dans la hierarchie
	 * Cette methode, recalcule tout les PVs de upline.
	 * Losque de l'insersion du compte, aucun bonus n'est recue, 
	 * aucune notification n'est generer
	 * @param Account $newAccount, nouveau compte
	 * @param Account $existAcount, ce compte doit exister n'avance dans l'arbre
	 * @throws DAOException si une erreur surviens dans la transaction
	 */
	public function insertBelow (Account $newAccount, Account $existAcount) : void;
    
	/**
	 * revoie le nombre de compte deja creer par un office
	 * @param int $officeId
	 * @return int
	 * @throws DAOException
	 */
	public function countByOffice(int $officeId) : int;
	/**
	 * Revoie le membre dot leurs compte on ete cree dans le bureau dont l'ID est en parametre
	 * @param int $officeId
	 * @param int $limit
	 * @param int $offset
	 * @return Member[]
	 * @throws DAOException
	 */
	public function findByOffice (int $officeId, ?int $limit = null, int $offset = 0) : array;
	
	/**
	 * l'office en parametre at-il deja creer aumoin un membre
	 * @param int $officeId
	 * @param int $limit
	 * @param int $offset
	 * @return bool
	 */
	public function checkByOffice (int $officeId, ?int $limit = null, int $offset = 0) : bool ;

    /**
     * 
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public function checkParent (int $memberId) : bool;
    
    /**
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public function checkSponsor (int $memberId) : bool;
    
    /**
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public function checkChilds (int $memberId) : bool;
    
    /**
     * @param int $memberId
     * @param int $foot
     * @return bool
     */
    public function checkChild (int $memberId, int $foot) : bool;
    
    
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function checkLeftChild (int $memberId) : bool;
    
    /**
     * verifie s'il y a un anfant su pied droit du membre en parmatre
     * @param int $memberId
     * @return bool
     */
    public function checkRightChild (int $memberId) : bool; 
    
    /**
     * verifie s'il y a un anfant sur le peid du milieux
     * @param int $memberId
     * @return bool
     */
    public function checkMiddelChild (int $memberId) : bool;
    
    /**
     * comptage des anfants du membre en parametre
     * @param int $memberId
     * @param int $foot
     * <ul>
     *  <li>null: compte tout les afants qui sont en dessous de</li>
     *  <li>1 ou Member::LEFT_FOOT: compte ceux qui sont sur la gauche</li>
     *  <li>2 ou Member::MIDDEL_FOOT: compte ceux qui sont sur le peid du milieux</li>
     *  <li>2 ou Member::RIGHT_FOOT: compte ceux qui sont sur le peid doit</li>
     * </ul>
     * @return int
     */
    public function countChilds (int $memberId, ?int $foot = null) : int;
    
    /**
     * comptage de tout les anfant qui c trouvent sur le peid gauche du membre dont l'id est en parametre
     * @param int $memberId
     * @return int
     */
    public function countLeftChild (int $memberId) : int;
    
    /**
     * comptage des tout les anfant qui c trouvement sur le pied droit du membre dont l'ID est en parametre
     * @param int $memberId
     * @return int
     */
    public function countMiddleChild (int $memberId) : int;
    
    /**
     * comptage des anfants qui ce trouvemnt sur le pieds droit de l'utilisateur en parametre
     * @param int $memberId
     * @return int
     */
    public function countRightChild (int $memberId) : int;
    
    /**
     * @param int $memberId
     * @param int $foot
     * @return array
     */
    public function findDownlinesChilds (int $memberId, ?int $foot = null) : array;
    
    /**
     *
     * @param int $memberId
     * @return int
     */
    public function findLeftDownlinesChilds (int $memberId) : array;
    
    /**
     *
     * @param int $memberId
     * @return int
     */
    public function findMiddleDownlinesChilds (int $memberId) : array;
    
    /**
     *
     * @param int $memberId
     * @return Member[]
     */
    public function findRightDownlinesChilds (int $memberId) : array;
    
    
    /**
     * revoie une collection des enfants d'un membre
     * les enfants en question sont empilee les unes dans les autres selons la hierchie
     * @param int $memberId
     * @param int $foot
     * @return Member[]
     */
    public function findDownlinesStacks (int $memberId, ?int $foot = null) : array;

    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function findLeftDownlineStack (int $memberId) : Member;
    
    /**
     * revoie la pile des membres sur le pied du milieux
     * @param int $memberId
     * @return Member
     */
    public function findMiddleDownlineStack (int $memberId) : Member;
    
    /**
     *revoie la olle de s afannts qui sont sur le pied droit de l'utilisateur en parametre
     * @param int $memberId
     * @return Member
     */
    public function findRightDownlineStack (int $memberId) : Member;    
    
    
    /**
     * renvoie le parent du membre en parmatre
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public function findParent (int $memberId) : Member;
    
    /**
     * renvoie le sponsor du membre en parametre
     * @param int $memberId
     * @throws DAOException
     * @return Member
     */
    public function findSponsor (int $memberId) : Member;
    
    /**
     * renvoie le membre directement en dessous du membre dont l'id est en parametre 
     * @param int $memberId
     * @return array
     * @throws DAOException
     */
    public function findChilds (int $memberId) : array;
    
    /**
     * 
     * @param int $memberId
     * @param int $foot
     * @return Member
     */
    public function findChild (int $memberId, int $foot) : Member;
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function findLeftChild (int $memberId) : Member;
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function findRightChild (int $memberId) : Member;
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function findMiddelChild (int $memberId) : Member;
    
    /**
     * mis en jour du matricule d'une membre
     * @param string $matricule
     * @param int $memberId
     */
    public function updateMatricule (string $matricule, int $memberId) : void ;
    
    /**
     * c matricule est-elle deja utiliser par un autre utilisateur??
     * @param string $matricule
     * @param int $id
     * @return bool
     */
    public function checkByMatricule (string $matricule, ?int $id = null) : bool ;
    
    /**
     * renvoie le membre propritaire du matricule en paramatre
     * @param string $matricule
     * @return Member
     */
    public function findByMatricule (string $matricule) : Member ;
    
    /**
     * verification de l'historique des operations effectuer pas un office
     * @param int $officeId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function checkCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool;
    
    /**
     * recuperation des l'historique des operations effectuer par un office
     * @param int $officeId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return Member[]
     */
    public function findCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array ;
    	
	/**
	 * comptage de operations effectuer par un office en une intervale de temps en parametres
	 * @param int $officeId
	 * @param \DateTime $dateMin
	 * @param \DateTime $dateMax
	 * @return int
	 */
	public function countCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null) : int;
	
	/**
	 * Effectue une recherche et renvoie les membres qui correspondent aux indices de recherche en parametre
	 * @param string[]|string $index
	 * @return Member[]
	 * @throws DAOException
	 */
	public function search ($index) : array;
	
}

