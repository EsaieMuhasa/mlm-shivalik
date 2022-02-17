<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\BonusGeneration;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\PointValue;
use Core\Shivalik\Entities\Withdrawal;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class MemberDAOManager extends AbstractUserDAOManager
{
	
	/**
	 * load all information for user account
	 * @param Member|int $member 
	 * @param bool $calcul
	 * @return Account
	 */
	public function loadAccount ($member, bool $calcul = true) : Account {
	    $account = new Account(($member instanceof Member)? $member : $this->findById($member));
	    
	    $daos = [PointValue::class, BonusGeneration::class, Withdrawal::class];
	    
	    foreach ($daos as $dao) {
	        /**
	         * @var AbstractOperationDAOManager $interface
	         */
	        $interface = $this->getManagerFactory()->getManagerOf($dao);
	        
	        if ($interface->checkByMember($account->getMember()->getId())) {
    	        $operations = $interface->findByMember($account->getMember()->getId());
    	        $account->addOperations($operations, false);
	        }
	        
	    }
	    
	    if ($calcul) {
    	    $account->calcul();
	    }
	    
	    return $account;
	}
	
    
	/**
	 * revoie le nombre de compte deja creer par un office
	 * @param int $officeId
	 * @return int
	 * @throws DAOException
	 */
	public function countByOffice(int $officeId) : int{
		if ($this->checkByOffice($officeId)) {
			return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), array('office' => $officeId));
		}
		return 0;
	}
	/**
	 * Revoie le membre dot leurs compte on ete cree dans le bureau dont l'ID est en parametre
	 * @param int $officeId
	 * @param int $limit
	 * @param int $offset
	 * @return Member[]
	 * @throws DAOException
	 */
	public function findByOffice (int $officeId, ?int $limit = null, int $offset = 0) {
		return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, array('office' => $officeId), $limit, $offset);
	}
	
	/**
	 * l'office en parametre at-il deja creer aumoin un membre
	 * @param int $officeId
	 * @return bool
	 */
	public function checkByOffice (int $officeId) : bool {
		return $this->columnValueExist('office', $officeId);
	}

    /**
     * 
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public abstract function checkParent (int $memberId) : bool;
    
    /**
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public abstract function checkSponsor (int $memberId) : bool;
    
    /**
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public abstract function checkChilds (int $memberId) : bool;
    
    /**
     * 
     * @param int $memberId
     * @param int $foot
     * @return bool
     */
    public abstract function checkChild (int $memberId, int $foot) : bool;
    
    
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function checkLeftChild (int $memberId) : bool{
        return $this->checkChild($memberId, Member::LEFT_FOOT);
    }
    
    /**
     * verifie s'il y a un anfant su pied droit du membre en parmatre
     * @param int $memberId
     * @return bool
     */
    public function checkRightChild (int $memberId) : bool{
        return $this->checkChild($memberId, Member::RIGHT_FOOT);
    }    
    
    /**
     * verifie s'il y a un anfant sur le peid du milieux
     * @param int $memberId
     * @return bool
     */
    public function checkMiddelChild (int $memberId) : bool{
        return $this->checkChild($memberId, Member::MIDDEL_FOOT);
    }
    
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
    public function countChilds (int $memberId, ?int $foot = null) : int{
        switch ($foot){
            case Member::LEFT_FOOT : {//left
                return $this->countLeftChild($memberId);
            }break;
            
            case Member::MIDDEL_FOOT : {//middle
                return $this->countMiddleChild($memberId);
            }break;
            
            case Member::RIGHT_FOOT : {//right
                return $this->countRightChild($memberId);
            }break;
            
            default : {//to count all member 
                $number = $this->countLeftChild($memberId);
                $number += $this->countMiddleChild($memberId);
                $number += $this->countRightChild($memberId);
                
                return $number;
            }
            
        }
    }
    
    /**
     * comptage de tout les anfant qui c trouvent sur le peid gauche du membre dont l'id est en parametre
     * @param int $memberId
     * @return int
     */
    public function countLeftChild (int $memberId) : int{
        $number = 0;
        
        if ($this->checkLeftChild($memberId)) {//s'il a un neud a gauche
            $leftChild = $this->findLeftChild($memberId);
            $number = 1;
            
            if ($this->checkChilds($leftChild->getId())) {//si le neud gauche a des afants
                $childs = $this->findChilds($leftChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $number++;
                    
                    if ($this->checkChilds($child->getId())) {//comptage des affant/afent
                        $number += $this->countChilds($child->getId());
                    }
                }
            }
            
        }
        
        return $number;
    }
    
    /**
     * comptage des tout les anfant qui c trouvement sur le pied droit du membre dont l'ID est en parametre
     * @param int $memberId
     * @return int
     */
    public function countMiddleChild (int $memberId) : int{
        $number = 0;
        
        if ($this->checkMiddelChild($memberId)) {//s'il a un neud au centre
            $middleChild = $this->findMiddelChild($memberId);
            $number = 1;
            
            if ($this->checkChilds($middleChild->getId())) {//si le neud au centre a des afants
                $childs = $this->findChilds($middleChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $number++;
                    
                    if ($this->checkChilds($child->getId())) {//comptage des affant/afent
                        $number += $this->countChilds($child->getId());
                    }
                }
            }
            
        }
        return $number;
    }
    
    /**
     * comptage des anfants qui ce trouvemnt sur le pieds droit de l'utilisateur en parametre
     * @param int $memberId
     * @return int
     */
    public function countRightChild (int $memberId) : int{
        $number = 0;
        
        if ($this->checkRightChild($memberId)) {//s'il a un neud a droite
            $ringhtChild = $this->findRightChild($memberId);
            $number = 1;
            
            if ($this->checkChilds($ringhtChild->getId())) {//si le neud au centre a des afants
                $childs = $this->findChilds($ringhtChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $number++;
                    
                    if ($this->checkChilds($child->getId())) {//comptage des affant/afent
                        $number += $this->countChilds($child->getId());
                    }
                }
            }
            
        }
        
        return $number;
    }
    
    
    /**
     * @param int $memberId
     * @param int $foot
     * @return array
     */
    public function findDownlinesChilds (int $memberId, ?int $foot = null) : array{
        
        switch ($foot){
            case Member::LEFT_FOOT : {//left
                return $this->findLeftDownlinesChilds($memberId);
            }break;
            
            case Member::MIDDEL_FOOT : {//middle
                return $this->findMiddleDownlinesChilds($memberId);
            }break;
            
            case Member::RIGHT_FOOT : {//right
                return $this->findRightDownlinesChilds($memberId);
            }break;
            
            default : {//all Member
                $members = $this->findLeftDownlinesChilds($memberId);
                $members = array_merge($members, $this->findMiddleDownlinesChilds($memberId));
                $members = array_merge($members, $this->findRightDownlinesChilds($memberId));
                return $members;
            }
            
        }
    }
    
    /**
     *
     * @param int $memberId
     * @return int
     */
    public function findLeftDownlinesChilds (int $memberId) : array{
        $members = array();
        
        if ($this->checkLeftChild($memberId)) {//s'il a un neud a gauche
            $leftChild = $this->findLeftChild($memberId);
            $members[] = $leftChild;
            
            if ($this->checkChilds($leftChild->getId())) {//si le neud gauche a des afants
                $childs = $this->findChilds($leftChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $members[] = $child;
                    if ($this->checkChilds($child->getId())) {
                        $members = array_merge($members, $this->findDownlinesChilds($child->getId()));
                    }
                }
            }
        }
        return $members;
    }
    
    /**
     *
     * @param int $memberId
     * @return int
     */
    public function findMiddleDownlinesChilds (int $memberId) : array{
        $members = array();
        
        if ($this->checkMiddelChild($memberId)) {//s'il a un neud au centre
            $middleChild = $this->findMiddelChild($memberId);
            $members[] = $middleChild;
            
            if ($this->checkChilds($middleChild->getId())) {//si le neud au centre a des afants
                $childs = $this->findChilds($middleChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $members[] = $child;
                    
                    if ($this->checkChilds($child->getId())) {
                        $members = array_merge($members, $this->findDownlinesChilds($child->getId()));
                    }
                }
            }
            
        }
        
        return $members;
    }
    
    /**
     *
     * @param int $memberId
     * @return Member[]
     */
    public function findRightDownlinesChilds (int $memberId) : array{
        $members = array();
        
        if ($this->checkRightChild($memberId)) {//s'il a un neud a droite
            $ringhtChild = $this->findRightChild($memberId);
            $members[] = $ringhtChild;
            
            if ($this->checkChilds($ringhtChild->getId())) {//si le neud as des neuds afant
                $childs = $this->findChilds($ringhtChild->getId());
                
                foreach ($childs as $child) {//comptage pout tout les afant
                    $members[] = $child;
                    if ($this->checkChilds($child->getId())) {
                        $members = array_merge($members, $this->findDownlinesChilds($child->getId()));
                    }
                }
            }
        }
        
        return $members;
    }
    
    
    /**
     * revoie une collection des enfants d'un membre
     * les enfants en question sont empilee les unes dans les autres selons la hierchie
     * @param int $memberId
     * @param int $foot
     * @return Member[]
     */
    public function findDownlinesStacks (int $memberId, ?int $foot = null) : array{
        
        $data = array();
        switch ($foot){
            case Member::LEFT_FOOT : {//left
                if ($this->checkLeftChild($memberId)) {
                    $data[] = $this->findLeftDownlineStack($memberId);
                }
            }break;
            
            case Member::MIDDEL_FOOT : {//middle
                if ($this->checkMiddelChild($memberId)) {
                    $data[] = $this->findMiddleDownlineStack($memberId);
                }
            }break;
            
            case Member::RIGHT_FOOT : {//right
                if ($this->checkRightChild($memberId)) {
                    $data[] = $this->getRightDownlineStack($memberId);
                }
            }break;
            
            default : {//all Member
                if ($this->checkLeftChild($memberId)) {
                    $data[] = $this->findLeftDownlineStack($memberId);
                }
                if ($this->checkMiddelChild($memberId)) {
                    $data[] = $this->findMiddleDownlineStack($memberId);
                }
                if ($this->checkRightChild($memberId)) {
                    $data[] = $this->findRightDownlineStack($memberId);
                }
            }
        }
        return $data;
    }

    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function findLeftDownlineStack (int $memberId) : Member{
        
        $leftChild = $this->findLeftChild($memberId);
        
        if ($this->checkChilds($leftChild->getId())) {//si le neud gauche a des afants
            $childs = $this->findChilds($leftChild->getId());
            
            foreach ($childs as $child) {//pour chaque noeud afant
                if ($this->checkChilds($child->getId())) {
                    $child->setChilds($this->findDownlinesStacks($child->getId()));//empilage de la methode parente
                }
            }
            
            $leftChild->setChilds($childs);
        }
        
        return $leftChild;
    }
    
    /**
     * revoie la pile des membres sur le pied du milieux
     * @param int $memberId
     * @return Member
     */
    public function findMiddleDownlineStack (int $memberId) : Member{
        
        $middleChild = $this->findMiddelChild($memberId);
        
        if ($this->checkChilds($middleChild->getId())) {//si le neud au centre a des afants
            $childs = $this->findChilds($middleChild->getId());
            
            foreach ($childs as $child) {//empilage des enfants -> des anfants
                if ($this->checkChilds($child->getId())) {
                    $child->setChilds($this->findDownlinesStacks($child->getId()));
                }
            }
            
            $middleChild->setChilds($childs);
        }
        
        return $middleChild;
    }
    
    /**
     *revoie la olle de s afannts qui sont sur le pied droit de l'utilisateur en parametre
     * @param int $memberId
     * @return Member
     */
    public function findRightDownlineStack (int $memberId) : Member{
        
        $ringhtChild = $this->findRightChild($memberId);
        
        if ($this->checkChilds($ringhtChild->getId())) {//si le neud as des neuds afant
            $childs = $this->findChilds($ringhtChild->getId());
            
            foreach ($childs as $child) {
                if ($this->checkChilds($child->getId())) {
                    $child->setChilds($this->findDownlinesStacks($child->getId()));
                }
            }
            
            $ringhtChild->setChilds($childs);
        }
        
        return $ringhtChild;
    }
    
    
    
    /**
     * renvoie le parent du membre en parmatre
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public function findParent (int $memberId) : Member{
        if ($this->checkParent($memberId)) {
            /**
             * @var Member $member
             */
            $member = $this->findById($memberId, false);
            return $this->findById($member->getParent()->getId());
        }
        
        throw new DAOException("this node does not have a parent node");
    }
    
    /**
     * renvoie le sponsor du membre en parametre
     * @param int $memberId
     * @throws DAOException
     * @return Member
     */
    public function findSponsor (int $memberId) : Member{
        if ($this->checkSponsor($memberId)) {
            /**
             * @var Member $member
             */
            $member = $this->findById($memberId, false);
            return $this->findById($member->getSponsor()->getId());
        }
        
        throw new DAOException("this node does not have a sponsor node");
    }
    
    /**
     * renvoie le membre directement en dessous du membre dont l'id est en parametre 
     * @param int $memberId
     * @return array
     * @throws DAOException
     */
    public function findChilds (int $memberId) : array{
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "foot", true, ['parent' => $memberId]);
    }
    
    /**
     * 
     * @param int $memberId
     * @param int $foot
     * @return Member
     */
    public abstract function findChild (int $memberId, int $foot) : Member;
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function findLeftChild (int $memberId) : Member{
        return $this->findChild($memberId, Member::LEFT_FOOT);
    }
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function findRightChild (int $memberId) : Member{
        return $this->findChild($memberId, Member::RIGHT_FOOT);
    }
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function findMiddelChild (int $memberId) : Member{
        return $this->findChild($memberId, Member::MIDDEL_FOOT);
    }
    
    /**
     * mis en jour du matricule d'une membre
     * @param string $matricule
     * @param int $memberId
     */
    public function updateMatricule (string $matricule, int $memberId) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('matricule' => $matricule), $memberId);
    }
    
    /**
     * c matricule est-elle deja utiliser par un autre utilisateur??
     * @param string $matricule
     * @param int $id
     * @return bool
     */
    public function checkByMatricule (string $matricule, ?int $id = null) : bool {
        return $this->columnValueExist('matricule', $matricule, $id);
    }
    
    /**
     * renvoie le membre propritaire du matricule en paramatre
     * @param string $matricule
     * @return Member
     */
    public function findByMatricule (string $matricule) : Member {
        return UtilitaireSQL::findUnique($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "matricule", $matricule);
    }
    
    /**
     * verification de l'historique des operations effectuer pas un office
     * @param int $officeId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function checkCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool {
        return UtilitaireSQL::hasCreationHistory($this->getConnection(), $this->getTableName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }
    
    /**
     * recuperation des l'historique des operations effectuer par un office
     * @param int $officeId
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @param int $limit
     * @param int $offset
     * @return Member[]
     */
    public function findCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array {
        return UtilitaireSQL::findCreationHistory($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }
    	
	/**
	 * comptage de operations effectuer par un office en une intervale de temps en parametres
	 * @param int $officeId
	 * @param \DateTime $dateMin
	 * @param \DateTime $dateMax
	 * @return int
	 */
	public function countCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null) : int{
	    return 0;
	}
	
	/**
	 * Effectue une recherche et renvoie les membres qui correspondent aux indices de recherche en parametre
	 * @param string[]|string $index
	 * @return Member[]
	 * @throws DAOException
	 */
	public abstract function search ($index) : array;
	
}

