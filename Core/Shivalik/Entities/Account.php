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
     * somme total des pv
     * @var double
     */
    private $pv;
    
    /**
     * @var double
     */
    private $leftPv;
    
    /**
     * @var double
     */
    private $rightPv;
    
    /**
     * @var double
     */
    private $middlePv;
    
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
        return $this->pv;
    }

    /**
     * @return number
     */
    public function getLeftPv()
    {
        return $this->leftPv;
    }

    /**
     * @return number
     */
    public function getRightPv()
    {
        return $this->rightPv;
    }

    /**
     * @return number
     */
    public function getMiddlePv()
    {
        return $this->middlePv;
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
            if ($operation instanceof Withdrawal) {
                if ($operation->getAdmin() == null ) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * @throws PHPBackendException
     */
    private function calculSolde () : void {
        $this->leftPv = 0;
        $this->rightPv = 0;
        $this->middlePv = 0;
        $this->wallet = 0;
        $this->pv = 0;
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
                    $this->leftPv += $operation->getValue();
                }else if ($operation->getFoot() == PointValue::FOOT_MIDDEL) {
                    $this->middlePv += $operation->getValue();
                }else if ($operation->getFoot() == PointValue::FOOT_RIGTH) {
                    $this->rightPv += $operation->getValue();
                }
                $this->pv += $operation->getAmount();
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

