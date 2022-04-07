<?php
namespace Core\Shivalik\Entities;

use PHPBackend\PHPBackendException;
use PHPBackend\Serialisation\JSONSerialize;
use PHPBackend\Serialisation\XMLSerialize;

/**
 *
 * @author Esaie MHS
 *        
 */
class Account
{
    
    /**
     * @var Member
     */
    private $member;
    
    /**
     * @var Operation[]
     */
    private $operations;
    
    
    /**
     * montant disponible dans un compte d'un patient
     * @var float
     */
    private $solde;
    
    /**
     * Le solde bonus office pour ceux qui en ont
     * @var float
     */
    private $soldeOfficeBonus;
    
    /**
     * Solde du compte principale (parainage)
     * @var float
     */
    private $soldeGenration;
    
    /**
     * @var number
     */
    private $leftMembershipPv;
    
    /**
     * @var number
     */
    private $rightMembershipPv;
    
    /**
     * @var number
     */
    private $middleMembershipPv;
    
    /**
     * @var number
     */
    private $leftProductPv;
    
    /**
     * @var number
     */
    private $rightProductPv;
    
    /**
     * @var number
     */
    private $middleProductPv;
    
    /**
     * effort personnel lors d'achat des produits
     * @var number
     */
    private $personalProductPv;
    
    /**
     * (bonus generationnnel et parainage)
     * @var double
     */
    private $wallet;

    
    /**
     * @var double
     */
    private $withdrawals;
    
    /**
     * @var double
     */
    private $withdrawalsRequest;
    

    use JSONSerialize, XMLSerialize;

    /**
     * @param Member $member
     * @param array $operations
     */
    public function __construct(Member $member, array $operations = array())
    {        
        $this->setMember($member);
        $this->setOperations($operations);
    }    
    
    /**
     * @return Member
     */
    public function getMember() : ?Member
    {
        return $this->member;
    }

    /**
     * @return multitype:Operation 
     */
    public function getOperations() : array
    {
        return $this->operations;
    }

    /**
     * @param Member $member
     */
    public function setMember(Member $member) : void
    {
        $this->member = $member;
    }

    /**
     * @param multitype:Operation  $operations
     */
    public function setOperations($operations)
    {
        $this->operations = $operations;
        $this->calculSolde();
    }
    
    /**
     * to add a operation in user account
     * @param Operation $operation
     * @param bool $run
     * @throws \InvalidArgumentException
     */
    public function addOperation (Operation $operation, bool $run = true) : void {
        if ($operation->getMember()->getId() != $this->getMember()->getId()) {
            throw new \InvalidArgumentException("invalid operation in addOperatins method");
        }
        
        $this->operations[] = $operation;
        if ($run) {
            $this->calculSolde();
        }
    }
    
    /**
     * ajout d'une collection des donnees dans un compte utilisateur
     * @param array $operations
     * @param bool $run
     */
    public function addOperations (array $operations, bool $run = true) : void {
        foreach ($operations as $operation) {
           $this->addOperation($operation, false);
        }
        
        if ($run) {
            $this->calcul();
        }
    }
    
    /**
     * pour obliger le compte a refaire tour les calculs
     */
    public function calcul () : void {
        $this->calculSolde();
    }
    
    /**
     * @return float
     */
    public function getSolde () : float {
        return $this->solde;
    }
    
    /**
     * @return number
     */
    public function getSoldeOfficeBonus()
    {
        return $this->soldeOfficeBonus;
    }

    /**
     * @return number
     */
    public function getSoldeGenration()
    {
        return $this->soldeGenration;
    }

    /**
     * @return number
     */
    public function getPv() 
    {
        return ($this->getMembershipPv() + $this->getProductPv());
    }
    
    /**
     * Return the somme of all membership points values
     * @return number
     */
    public function getMembershipPv () {
        return ($this->getLeftMembershipPv() + $this->getMiddleMembershipPv() + $this->getRightMembershipPv());
    }
    
    /**
     * return the somme of all product points values
     * @return number
     */
    public function getProductPv () {
        return ($this->getLeftProductPv() + $this->getMiddleProductPv() + $this->getRightProductPv() + $this->getPersonalProductPv());
    }

    /**
     * return personal point value
     * @return number
     */
    public function getPersonalProductPv()
    {
        return $this->personalProductPv;
    }

    /**
     * Return sold of point value at left foot of this account
     * @return number
     */
    public function getLeftPv()
    {
        return ($this->getLeftMembershipPv() + $this->getLeftProductPv());
    }

    /**
     * return sold of point value at right foot of account
     * @return number
     */
    public function getRightPv()
    {
        return ($this->getRightMembershipPv() + $this->getRightProductPv());
    }

    /**
     * @return number
     */
    public function getMiddlePv()
    {
        return ($this->getMiddleMembershipPv() + $this->getMiddleProductPv());
    }

    /**
     * @return number
     */
    public function getLeftMembershipPv()
    {
        return $this->leftMembershipPv;
    }

    /**
     * @return number
     */
    public function getRightMembershipPv()
    {
        return $this->rightMembershipPv;
    }

    /**
     * @return number
     */
    public function getMiddleMembershipPv()
    {
        return $this->middleMembershipPv;
    }

    /**
     * @return number
     */
    public function getLeftProductPv()
    {
        return $this->leftProductPv;
    }

    /**
     * @return number
     */
    public function getRightProductPv()
    {
        return $this->rightProductPv;
    }

    /**
     * @return number
     */
    public function getMiddleProductPv()
    {
        return $this->middleProductPv;
    }
    
    /**
     * Renvoie la collection des points, pour tout les operations du compte
     * @return PointValue[]
     */
    public function getPointValues () {
        $pvs = [];
        foreach ($this->operations as $opt) {
            if($opt instanceof PointValue)
                $pvs[] = $opt;
        }
        return $pvs;
    }

    /**
     * @return number
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * @return float
     */
    public function getWithdrawals () : float{
        return $this->withdrawals;
    }
    
    /**
     * @return float
     */
    public function getWithdrawRequest () : float{
        return $this->withdrawalsRequest;
    }
    
    /**
     * ya-t il un retrait en attante??
     * @return bool
     */
    public function hasWithdrawRequest () : bool {
        foreach ($this->operations as $operation) {
            if ($operation instanceof Withdrawal && $operation->getAdmin() == null ) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * @throws PHPBackendException
     */
    private function calculSolde () : void {
        $this->leftMembershipPv = 0;
        $this->rightMembershipPv = 0;
        $this->middleMembershipPv = 0;
        $this->leftProductPv = 0;
        $this->rightProductPv = 0;
        $this->middleProductPv = 0;
        $this->personalProductPv = 0;
        $this->wallet = 0;
        $this->withdrawals = 0;
        $this->withdrawalsRequest = 0;
        $this->soldeOfficeBonus = 0;
        $this->soldeGenration = 0.0;
        
        foreach ($this->getOperations() as $operation) {
            if ($operation instanceof Withdrawal) {
            	
                if ($operation->getAdmin() != null) {                    
                    $this->withdrawals += $operation->getAmount();
                }else {
                    $this->withdrawalsRequest += $operation->getAmount();
                }
                
            }else if ($operation instanceof BonusGeneration) {
                $this->soldeGenration += $operation->getAmount();
            } else if ($operation instanceof OfficeBonus) {
                $this->soldeOfficeBonus += $operation->getAmount();
            }else if ($operation instanceof PointValue) {
                if ($operation->getFoot() == PointValue::FOOT_LEFT) {
                    if ($operation->getCommand() == null) {
                        $this->leftMembershipPv += $operation->getValue();
                    } else {
                        $this->leftProductPv += $operation->getValue();
                    }
                }else if ($operation->getFoot() == PointValue::FOOT_MIDDEL) {
                    if ($operation->getCommand() == null) {
                        $this->middleMembershipPv += $operation->getValue();
                    } else {
                        $this->middleProductPv += $operation->getValue();
                    }
                }else if ($operation->getFoot() == PointValue::FOOT_RIGTH) {
                    if ($operation->getCommand() == null) {
                        $this->rightMembershipPv += $operation->getValue();
                    } else {
                        $this->rightProductPv += $operation->getValue();
                    }
                } else if ($operation->getCommand() != null) {//effort personnel
                    $this->personalProductPv += $operation->getValue();
                } else {
                    throw new PHPBackendException("Invalid data integrity. Unable to classify point value");
                }
            }else if ($operation instanceof Transfer) {
                if ($this->member->getId() == $operation->getReceiver()->getId()) {
                    //reception de l'argement
                } else {
                    //transfert de l'argent
                }
            }else {
                throw new PHPBackendException("unable to calculate account balance for {$this->getMember()->getNames()}");
            }
        }
        
        $this->solde = $this->soldeGenration + $this->soldeOfficeBonus + $this->withdrawalsRequest - $this->withdrawals;
    }

}

