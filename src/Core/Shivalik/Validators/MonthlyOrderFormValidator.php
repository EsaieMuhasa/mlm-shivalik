<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Managers\MonthlyOrderDAOManager;
use Core\Shivalik\Entities\MonthlyOrder;
use PHPBackend\PHPBackendException;
use PHPBackend\Validator\IllegalFormValueException;
use PHPBackend\Dao\DAOException;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Entities\Member;

/**
 *
 * @author Esaie MUHASA
 * @deprecated 12/09/2022
 * pour des raisons de gestion la fiche de vente pour chaque membres dans le systeme, il est maintenant decoseiller de 
 * passer par cette classe pour enregistrer le bonus de reachat    
 */
class MonthlyOrderFormValidator extends AbstractOperationFormValidator {
    
    const FIELD_MEMBER_ID = 'memberId';
    const FIELD_OFFICE = 'office';
    
    /**
     * @var MonthlyOrderDAOManager
     */
    private $monthlyOrderDAOManager;
    
    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;
    
    /**
     * process to validate member Id
     * @param string $memberId
     * @throws IllegalFormValueException
     */
    private function validationMember ($memberId) : void {
        if ($memberId == null) {
            throw  new IllegalFormValueException("Member ID is required");
        } else {
            try {
                if (!$this->memberDAOManager->checkByMatricule($memberId)) {
                    throw new IllegalFormValueException("uknow member ID in shivalik center database");
                }
            } catch (DAOException $e) {
                throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\AbstractOperationFormValidator::processingAmount()
     */
    protected function processingAmount(\Core\Shivalik\Entities\Operation $operation, $amount): void {
        parent::processingAmount($operation, $amount);
        if (!$this->hasError(self::FIELD_AMOUNT)) {
            $operation->setManualAmount($amount);
        }
    }

    /**
     * call to validation process end finalize member ID validation process
     * @param string $memberId
     * @param MonthlyOrder $order
     */
    private function processingMember ($memberId, MonthlyOrder $order) : void {
        try {
            $this->validationMember($memberId);
            $order->setMember($this->memberDAOManager->findByMatricule($memberId));
        } catch (IllegalFormValueException $e) {
            $order->setMember(new Member());
            $order->getMember()->setMatricule($memberId);
            $this->addError(self::FIELD_MEMBER_ID, $e->getMessage());
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return MonthlyOrder
     */
    public function createAfterValidation(\PHPBackend\Request $request) {
        $order = new MonthlyOrder();
        $amount = $request->getDataPOST(self::FIELD_AMOUNT);
        $memberId = $request->getDataPOST(self::FIELD_MEMBER_ID);
        $office = $request->getAttribute(self::FIELD_OFFICE);
        
        $this->processingAmount($order, $amount);
        $this->processingMember($memberId, $order);
        $order->setOffice($office);
        
        if (!$this->hasError()) {
            try {
                if($this->monthlyOrderDAOManager->checkByMemberOfMonth($order->getMember()->getId(), true)) {
                    $old = $this->monthlyOrderDAOManager->findByMemberOfMonth($order->getMember()->getId(), true);
                    if ($old->getManualAmount() == $order->getManualAmount()) {
                        throw new DAOException("You cannot perform this operation. We have auther monthly bonus of this month to {$memberId} member ID");
                    }
                }
                $this->monthlyOrderDAOManager->dispatchManualPurchaseBonus($order);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->setResult("successfully to dispatch monthly bonus", "Failure to dispatch monthly purchase bonus");
        return $order;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     * @return MonthlyOrder
     */
    public function updateAfterValidation(\PHPBackend\Request $request) {
        throw new PHPBackendException("You cannot perform update operation of monthly purchase managed manual at member file in office");
    }

}

