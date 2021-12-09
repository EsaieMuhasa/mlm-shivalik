<?php
namespace Managers;

use Entities\Member;
use Library\DAOException;
use Entities\Account;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class MemberDAOManager extends AbstractUserDAOManager
{
    
	/**
	 * revoie le nombre de compte deja creer par un office
	 * @param int $officeId
	 * @return int
	 * @throws DAOException
	 */
	public function countCreatedBy(int $officeId) : int{
		if ($this->hasAlreadyCreate($officeId)) {
			return count($this->getCreatedBy($officeId));
		}
		return 0;
	}
	
	/**
	 * collectionne les informations d'un compte et lance les calculs dans le cas où le deuxième parametre veau true
	 * @param Member $member
	 * @param bool $calcul
	 * @return Account
	 */
	public function getAccount (Member $member, bool $calcul=true) : Account {
	    $compte = new Account($member);
	    
	    //Chargement des PV;
	    if ($this->getDaoManager()->getManagerOf("PointValue")->hasLeft($member->getId())) {
	        $left = $this->getDaoManager()->getManagerOf("PointValue")->leftOfMember($member->getId());
	        foreach ($left as $l) {
	            $compte->addOperation($l, false);
	        }
	    }
	    
	    if ($this->getDaoManager()->getManagerOf("PointValue")->hasRight($member->getId())) {
	        $right = $this->getDaoManager()->getManagerOf("PointValue")->rightOfMember($member->getId());
	        foreach ($right as $r) {
	            $compte->addOperation($r, false);
	        }
	    }
	    
	    if ($this->getDaoManager()->getManagerOf("PointValue")->hasMiddle($member->getId())) {
	        $middle = $this->getDaoManager()->getManagerOf("PointValue")->middleOfMember($member->getId());
	        foreach ($middle as $m) {
	            $compte->addOperation($m, false);
	        }
	    }
	    
	    //bonus
	    if ($this->hasChilds($member->getId())) {
	        if ($this->getDaoManager()->getManagerOf("BonusGeneration")->hasBonus($member->getId())) {
	            $bonus = $this->getDaoManager()->getManagerOf("BonusGeneration")->forMember($member->getId());
	            foreach ($bonus as $b) {
	                $compte->addOperation($b, false);
	            }
	        }
	    }
	    
	    //office bonus
	    if ($this->getDaoManager()->getManagerOf("Office")->hasOffice($member->getId())) {
	        $office = $this->getDaoManager()->getManagerOf("Office")->forMember($member->getId());
	        if ($this->getDaoManager()->getManagerOf("OfficeBonus")->hasOperation($member->getId())) {
	            $officeBonus = $this->getDaoManager()->getManagerOf("OfficeBonus")->forMember($member->getId());
	            
	            foreach ($officeBonus as $ob) {
	                $compte->addOperation($ob);
	            }
	        }
	        
	        $member->setOfficeAccount($office);
	    }
	    
	    //retraits
	    if ($this->getDaoManager()->getManagerOf("Withdrawal")->hasOperation($member->getId())) {
	        $withdrawals = $this->getDaoManager()->getManagerOf("Withdrawal")->forMember($member->getId());
	        foreach ($withdrawals as $withdrawal) {
	            $compte->addOperation($withdrawal, false);
	        }
	    }
	    
	    $compte->calcul();
	    return $compte;
	}
	
	/**
	 * Revoie le membre dot leurs compte on ete cree dans le bureau dont l'ID est en parametre
	 * @param int $officeId
	 * @param int $limit
	 * @param int $offset
	 * @return Member[]
	 * @throws DAOException
	 */
	public function getCreatedBy (int $officeId, $limit = -1, $offset = -1) {
		return $this->pdo_fromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'office', $officeId, $limit, $offset);
	}
	
	/**
	 * l'office en parametre at-il deja creer aumoin un membre
	 * @param int $officeId
	 * @return bool
	 */
	public function hasAlreadyCreate (int $officeId) : bool {
		return $this->pdo_columnValueExistInTable($this->getTableName(), 'office', $officeId);
	}
	
    /**
     * 
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public abstract function hasParent (int $memberId) : bool;
    
    
    /**
     * 
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public abstract function hasSponsor (int $memberId) : bool;
    
    /**
     * 
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public abstract function hasChilds (int $memberId) : bool;
    
    /**
     * 
     * @param int $memberId
     * @param int $foot
     * @return bool
     */
    public abstract function hasChild (int $memberId, int $foot) : bool;
    
    
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function hasLeftChild (int $memberId) : bool{
        return $this->hasChild($memberId, Member::LEFT_FOOT);
    }
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function hasRightChild (int $memberId) : bool{
        return $this->hasChild($memberId, Member::RIGHT_FOOT);
    }
    
    
    /**
     * 
     * @param int $memberId
     * @return bool
     */
    public function hasMiddelChild (int $memberId) : bool{
        return $this->hasChild($memberId, Member::MIDDEL_FOOT);
    }
    
    /**
     * @param int $memberId
     * @param int $foot
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
            
            default : {//all Member
                $number = $this->countLeftChild($memberId);
                $number += $this->countMiddleChild($memberId);
                $number += $this->countRightChild($memberId);
                
                return $number;
            }
            
        }
    }
    
    /**
     * 
     * @param int $memberId
     * @return int
     */
    public function countLeftChild (int $memberId) : int{
        $number = 0;
        
        if ($this->hasLeftChild($memberId)) {//s'il a un neud a gauche
            $leftChild = $this->getLeftChild($memberId);
            $number = 1;
            
            if ($this->hasChilds($leftChild->getId())) {//si le neud gauche a des afants
                $childs = $this->getChilds($leftChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $number++;
                    
                    if ($this->hasChilds($child->getId())) {//comptage des affant/afent
                        $number += $this->countChilds($child->getId());
                    }
                }
            }
            
        }
        return $number;
    }
    
    /**
     * 
     * @param int $memberId
     * @return int
     */
    public function countMiddleChild (int $memberId) : int{
        $number = 0;
        
        if ($this->hasMiddelChild($memberId)) {//s'il a un neud au centre
            $middleChild = $this->getMiddelChild($memberId);
            $number = 1;
            
            if ($this->hasChilds($middleChild->getId())) {//si le neud au centre a des afants
                $childs = $this->getChilds($middleChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $number++;
                    
                    if ($this->hasChilds($child->getId())) {//comptage des affant/afent
                        $number += $this->countChilds($child->getId());
                    }
                }
            }
            
        }
        
        return $number;
    }
    
    /**
     * 
     * @param int $memberId
     * @return int
     */
    public function countRightChild (int $memberId) : int{
        $number = 0;
        
        if ($this->hasRightChild($memberId)) {//s'il a un neud a droite
            $ringhtChild = $this->getRightChild($memberId);
            $number = 1;
            
            if ($this->hasChilds($ringhtChild->getId())) {//si le neud au centre a des afants
                $childs = $this->getChilds($ringhtChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $number++;
                    
                    if ($this->hasChilds($child->getId())) {//comptage des affant/afent
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
    public function getDownlinesChilds (int $memberId, ?int $foot = null) : array{
        
        switch ($foot){
            case Member::LEFT_FOOT : {//left
                return $this->getLeftDownlinesChilds($memberId);
            }break;
            
            case Member::MIDDEL_FOOT : {//middle
                return $this->getMiddleDownlinesChilds($memberId);
            }break;
            
            case Member::RIGHT_FOOT : {//right
                return $this->getRightDownlinesChilds($memberId);
            }break;
            
            default : {//all Member
                $members = $this->getLeftDownlinesChilds($memberId);
                $members = array_merge($members, $this->getMiddleDownlinesChilds($memberId));
                $members = array_merge($members, $this->getRightDownlinesChilds($memberId));
                return $members;
            }
            
        }
    }
    
    /**
     *
     * @param int $memberId
     * @return int
     */
    public function getLeftDownlinesChilds (int $memberId) : array{
        $members = array();
        
        if ($this->hasLeftChild($memberId)) {//s'il a un neud a gauche
            $leftChild = $this->getLeftChild($memberId);
            $members[] = $leftChild;
            
            if ($this->hasChilds($leftChild->getId())) {//si le neud gauche a des afants
                $childs = $this->getChilds($leftChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $members[] = $child;
                    if ($this->hasChilds($child->getId())) {
                        $members = array_merge($members, $this->getDownlinesChilds($child->getId()));
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
    public function getMiddleDownlinesChilds (int $memberId) : array{
        $members = array();
        
        if ($this->hasMiddelChild($memberId)) {//s'il a un neud au centre
            $middleChild = $this->getMiddelChild($memberId);
            $members[] = $middleChild;
            
            if ($this->hasChilds($middleChild->getId())) {//si le neud au centre a des afants
                $childs = $this->getChilds($middleChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $members[] = $child;
                    
                    if ($this->hasChilds($child->getId())) {
                        $members = array_merge($members, $this->getDownlinesChilds($child->getId()));
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
    public function getRightDownlinesChilds (int $memberId) : array{
        $members = array();
        
        if ($this->hasRightChild($memberId)) {//s'il a un neud a droite
            $ringhtChild = $this->getRightChild($memberId);
            $members[] = $ringhtChild;
            
            if ($this->hasChilds($ringhtChild->getId())) {//si le neud as des neuds afant
                $childs = $this->getChilds($ringhtChild->getId());
                
                foreach ($childs as $child) {//comptage pout tout les afant
                    $members[] = $child;
                    if ($this->hasChilds($child->getId())) {
                        $members = array_merge($members, $this->getDownlinesChilds($child->getId()));
                    }
                }
            }
        }
        
        return $members;
    }
    
    
    /**
     * revoie une collection de afent d'un membre
     * les enfant en question sont empilee les unes dans les autres selons la hierchie
     * @param int $memberId
     * @param int $foot
     * @return Member[]
     */
    public function getDownlinesStacks (int $memberId, ?int $foot = null) : array{
        
        $data = array();
        switch ($foot){
            case Member::LEFT_FOOT : {//left
                if ($this->hasLeftChild($memberId)) {
                    $data[] = $this->getLeftDownlineStack($memberId);
                }
            }break;
            
            case Member::MIDDEL_FOOT : {//middle
                if ($this->hasMiddelChild($memberId)) {
                    $data[] = $this->getMiddleDownlineStack($memberId);
                }
            }break;
            
            case Member::RIGHT_FOOT : {//right
                if ($this->hasRightChild($memberId)) {
                    $data[] = $this->getRightDownlineStack($memberId);
                }
            }break;
            
            default : {//all Member
                if ($this->hasLeftChild($memberId)) {
                    $data[] = $this->getLeftDownlineStack($memberId);
                }
                if ($this->hasMiddelChild($memberId)) {
                    $data[] = $this->getMiddleDownlineStack($memberId);
                }
                if ($this->hasRightChild($memberId)) {
                    $data[] = $this->getRightDownlineStack($memberId);
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
    public function getLeftDownlineStack (int $memberId) : Member{
        
        $leftChild = $this->getLeftChild($memberId);
        
        if ($this->hasChilds($leftChild->getId())) {//si le neud gauche a des afants
            $childs = $this->getChilds($leftChild->getId());
            
            foreach ($childs as $child) {//pour chaque noeud afant
                if ($this->hasChilds($child->getId())) {
                    $child->setChilds($this->getDownlinesStacks($child->getId()));//empilage de la methode parente
                }
            }
            
            $leftChild->setChilds($childs);
        }
        
        return $leftChild;
    }
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function getMiddleDownlineStack (int $memberId) : Member{
        
        $middleChild = $this->getMiddelChild($memberId);
        
        if ($this->hasChilds($middleChild->getId())) {//si le neud au centre a des afants
            $childs = $this->getChilds($middleChild->getId());
            
            foreach ($childs as $child) {//empilage des enfants -> des anfants
                if ($this->hasChilds($child->getId())) {
                    $child->setChilds($this->getDownlinesStacks($child->getId()));
                }
            }
            
            $middleChild->setChilds($childs);
        }
        
        return $middleChild;
    }
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function getRightDownlineStack (int $memberId) : Member{
        
        $ringhtChild = $this->getRightChild($memberId);
        
        if ($this->hasChilds($ringhtChild->getId())) {//si le neud as des neuds afant
            $childs = $this->getChilds($ringhtChild->getId());
            
            foreach ($childs as $child) {
                if ($this->hasChilds($child->getId())) {
                    $child->setChilds($this->getDownlinesStacks($child->getId()));
                }
            }
            
            $ringhtChild->setChilds($childs);
        }
        
        return $ringhtChild;
    }
    
    
    
    /**
     *
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public abstract function getParent (int $memberId) : Member;
    
    /**
     *
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public abstract function getSponsor (int $memberId) : Member;
    
    /**
     *
     * @param int $memberId
     * @return bool
     * @throws DAOException
     */
    public abstract function getChilds (int $memberId) : array;
    
    /**
     * 
     * @param int $memberId
     * @param int $foot
     * @return Member
     */
    public abstract function getChild (int $memberId, int $foot) : Member;
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function getLeftChild (int $memberId) : Member{
        return $this->getChild($memberId, Member::LEFT_FOOT);
    }
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function getRightChild (int $memberId) : Member{
        return $this->getChild($memberId, Member::RIGHT_FOOT);
    }
    
    /**
     *
     * @param int $memberId
     * @return Member
     */
    public function getMiddelChild (int $memberId) : Member{
        return $this->getChild($memberId, Member::MIDDEL_FOOT);
    }
    
    /**
     * 
     * @param string $matricule
     * @param int $memberId
     */
    public function updateMatricule (string $matricule, int $memberId) : void {
        $this->pdo_updateInTable($this->getTableName(), array('matricule' => $matricule), $memberId, false);
    }
    
    /**
     * 
     * @param string $matricule
     * @param int $id
     * @return bool
     */
    public function matriculeExist (string $matricule, int $id = -1) : bool {
        return $this->columnValueExist('matricule', $matricule, $id);
    }
    
    /**
     * 
     * @param string $matricule
     * @return Member
     */
    public function getForMatricule (string $matricule) : Member {
        return $this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'matricule', $matricule);
    }
    
    
}

