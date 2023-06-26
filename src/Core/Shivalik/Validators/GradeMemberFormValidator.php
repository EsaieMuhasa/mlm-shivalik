<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use PHPBackend\PHPBackendException;
use PHPBackend\Request;
use PHPBackend\Dao\DAOException;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;
use Core\Shivalik\Managers\MonthlyOrderDAOManager;
use Applications\Office\Modules\Members\MembersController;
use Core\Shivalik\Entities\Grade;
use Core\Shivalik\Entities\MonthlyOrder;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Managers\OfficeDAOManager;
use DateTime;

/**
 *
 * @author Esaie MHS
 *        
 */
class GradeMemberFormValidator extends DefaultFormValidator
{
    const FIELD_GRADE = 'grade';
    const FIELD_MEMBER = 'member';
    const FIELD_MEMBERSHIP = 'membership';
    const FIELD_PRODUCT = 'product';
    const FIELD_OFFICE_ADMIN = 'officeAdmin';
    
    /**
     * @var GradeMemberDAOManager
     */
    private $gradeMemberDAOManager;
    
    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;
    
    /**
     * @var GradeDAOManager
     */
    private $gradeDAOManager;
    
    /**
     * @var MonthlyOrderDAOManager
     */
    private $monthlyOrderDAOManager;
    
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    /**
     * validation du packet soliciter par le membre
     * @param string|int $grade
     * @throws IllegalFormValueException
     */
    private function validationGrade ($grade) : void {
        if ($grade == null) {
            throw new IllegalFormValueException("make sure you select the grade");
        }elseif (!preg_match(self::RGX_INT_POSITIF, $grade)) {
            throw new IllegalFormValueException("the grade reference must be a positive numeric value");
        }
    }
    
    /**
     * validation d'un identifiant d'un grade d'un membre
     * {@inheritDoc}
     * @see \PHPBackend\Validator\DefaultFormValidator::validationId()
     */
    protected function validationId($id): void {
        parent::validationId($id);
        
        try {
            if (!$this->gradeMemberDAOManager->checkById(intval($id, 10))) {
                throw new DAOException("unknown user rank in the system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * validation du membre proprietaire deu packet
     * @param int $member
     * @throws IllegalFormValueException
     */
    private function validationMember ($member) : void {
        if ($member == null) {
            throw new IllegalFormValueException("the reference of the member concerned is mandatory");
        }elseif (!preg_match(self::RGX_INT_POSITIF, $member)) {
            throw new IllegalFormValueException("member reference must be a positive numeric value");
        }
        
        try {
            if (!$this->memberDAOManager->checkById(intval($member, 10))) {
                throw new IllegalFormValueException("the referemce of member is invalid");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * validation du matricule d'un membre
     *
     * @param string $member
     * @return void
     * @throws IllegalFormValueException
     */
    private function validationMatriculMember (?string $member) : void {
        if ($member == null) {
            throw new IllegalFormValueException("the reference of the member concerned is mandatory");
        }
        
        try {
            if (!$this->memberDAOManager->checkByMatricule($member)) {
                throw new IllegalFormValueException("the referemce of member is invalid");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * processuce de validation et traitement du matricule d'un membre
     *
     * @param GradeMember $gm
     * @param string $matricul
     * @return void
     * @throws IllegalFormValueException
     */
    private function processingMatriculMember (GradeMember $gm, $matricul) : void {
        try {
            $this->validationMatriculMember($matricul);
            $gm->setMember($this->memberDAOManager->findByMatricule($matricul));
        } catch (IllegalFormValueException $e) {
            $this->addMessage(self::FIELD_MEMBER, $e->getMessage());
        }
    }
    
    /**
     * validation du montant d'adhesion, payer par le membre du syndicat
     * @param float $membership
     * @throws IllegalFormValueException
     */
    private function validationMembership ($membership) : void {
        if ($membership == null) {
            throw new IllegalFormValueException("the membership fee is mandatory");
        }elseif (!preg_match(self::RGX_NUMERIC_POSITIF, $membership)){
            throw new IllegalFormValueException("the membership amount must be a positive numeric value");
        }
    }
    
    /**
     * validation du montant considerer comme achat produit lors de l'adhesion d'un membre
     * @param float $product
     * @throws IllegalFormValueException
     */
    private function validationProduct ($product) : void {
        if ($product == null) {
            throw new IllegalFormValueException("the amount allocated to the products is mandatory");
        }elseif (!preg_match(self::RGX_NUMERIC_POSITIF, $product)){
            throw new IllegalFormValueException("the amount allocated to the products must be a positive numerical value");
        }
    }
    
    /**
     * processuce de traitemet/validation du packet soliciter par le membre
     * @param GradeMember $gm
     * @param int $grade
     */
    private function processingGrade (GradeMember $gm, $grade) : void {
        try {
            $this->validationGrade($grade);
            $gm->setGrade($grade);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_GRADE, $e->getMessage());
        }
    }
    
    /**
     * processuce de traitement/validation du membre qui solicite l'inscription au nouveau packet
     * @param GradeMember $gm
     * @param Member|int $member une instace de Member soit l'ID d'un membre du syndicat
     */
    private function processingMember (GradeMember $gm, $member) : void {
        try {
            if ($member instanceof Member) {
                $gm->setMember($member);
                return;
            }
            $this->validationMember($member);
            $gm->setMember($member);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_MEMBER, $e->getMessage());
        }
    }
    
    
    /**
     * validation/traitement des frais d'adhesion
     * @param GradeMember $gm
     * @param number $membership  le frais d'adhesion (a envoyer a la hirarchie)
     * @param number $officePart (le frais de fonctionnement bu bureau)
     */
    private function processingMembership (GradeMember $gm, $membership, $officePart) : void {
        try {
            $this->validationMembership($membership);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_MEMBERSHIP, $e->getMessage());
        }
        $gm->setMembership($membership);
        $gm->setOfficePart($officePart);
    }
    
    /**
     * validation du montant aloue a l'achat des produit
     * @param GradeMember $gm
     * @param number $product
     */
    private function processingProduct (GradeMember $gm, $product) : void {
        try {
            $this->validationProduct($product);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PRODUCT, $e->getMessage());
        }
        $gm->setProduct($product);
    }
    
    
    /**
     * la creation d'un grade d'un membre inclue 
     * -la validation temporel du membre
     * -la validation temporel de la localisation du membre
     * -et en fin le grade meme soliciter par le membre
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return GradeMember
     */
    public function createAfterValidation(Request $request) {
        $gm = new GradeMember();
        $grade = $request->getDataPOST(self::FIELD_GRADE);
        
        //validation du membre
        $formMember = new MemberFormValidator($this->getDaoManager());
        $member = $formMember->processingMember($request);
        
        //validation de l'adresse du membre
        $formLocalisation = new LocalisationFormValidator($this->getDaoManager());
        $localisation = $formLocalisation->processingLocalisation($request);
        $member->setLocalisation($localisation);
        
        $this->addFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK, $formLocalisation->toFeedback());
        $this->addFeedback(MemberFormValidator::MEMBER_FEEDBACK, $formMember->toFeedback());
        
        $this->processingGrade($gm, $grade);
        $this->processingMember($gm, $member);
        
        $gm->setMember($member);
        
        if (!$this->hasError()) {
        	
            $gm->setInitDate(new \DateTime());
            $gm->getMember()->setAdmin($request->getAttribute(self::FIELD_OFFICE_ADMIN));
            $gm->getMember()->setOffice($gm->getMember()->getAdmin()->getOffice());
            $gm->setOffice($gm->getMember()->getAdmin()->getOffice());
            $gm->setGrade($this->gradeDAOManager->findById($gm->getGrade()->getId()));
            $this->processingProduct($gm, $gm->getGrade()->getProductAmount());
            $this->processingMembership($gm, $gm->getGrade()->getMembershipAmount(), $gm->getGrade()->getOfficeAmount());
            
            // --verification de la monais virtuel
            $product = $gm->getProduct();
            $membership = $gm->getGrade()->getMembershipAmount();
            
            if ($product > $member->getOffice()->getAvailableVirtualMoneyProduct() || $membership >  $member->getOffice()->getAvailableVirualMoneyAfiliate()) {
                $message = "impossible to perform this operation because the office wallet is insufficient. requered product money: {$product} {$request->getApplication()->getConfig()->get('devise')}, ";
                $message .= "requered membership money: {$membership} {$request->getApplication()->getConfig()->get('devise')}, ";
                $message .= "your product wallet: {$member->getOffice()->getAvailableVirtualMoneyProduct()} {$request->getApplication()->getConfig()->get('devise')}";
                $message .= "your membership wallet: {$member->getOffice()->getAvailableVirualMoneyAfiliate()} {$request->getApplication()->getConfig()->get('devise')}";
            	$this->setMessage($message);
            }
            // \\--
            
            if (!$this->hasError()) {
                try {
                    $this->officeDAOManager->load($gm->getOffice());
                    $this->gradeMemberDAOManager->create($gm);
                    $this->officeDAOManager->load($gm->getOffice());
                    if ($formMember->getProcessPhoto()->isFile()) {
                        $formMember->processingPhoto($member, $formMember->getProcessPhoto(), true, $request->getApplication()->getConfig());
                    }else{
                        $member->setPhoto('img/user.png');
                    }
                    $this->memberDAOManager->updatePhoto($member->getId(), $member->getPhoto());
                } catch (DAOException $e) {
                    $this->setMessage($e->getMessage());
                }
            }
        }
        
        $this->result = $this->hasError()? "Failure to register member" : "successful registration of member";
        
        return $gm;
        
    }
    
    /**
     * to upgrage the status of member
     * @param Request $request
     * @return GradeMember
     */
    public function upgradeAfterValidation (Request $request) : GradeMember{
        $gm = new GradeMember();
        $gradeId = $request->getDataPOST(self::FIELD_GRADE);
        $memberId = $request->getAttribute(self::FIELD_MEMBER);
        
        $this->processingGrade($gm, $gradeId);
        $this->processingMember($gm, $memberId);
        
        if (!$this->hasError()) {
            $gm->getMember()->setAdmin($request->getAttribute(self::FIELD_OFFICE_ADMIN));
            $gm->getMember()->setOffice($gm->getMember()->getAdmin()->getOffice());
            try {
                
                $member = $this->memberDAOManager->findById(intval($memberId, 10));
                $old = $this->gradeMemberDAOManager->findCurrentByMember($member->getId());
                
                /**
                 * @var Grade $require
                 */
                $require = $this->gradeDAOManager->findById($gradeId);
                
                $product = $require->getProductAmount() - $old->getGrade()->getProductAmount();
                
                $this->processingProduct($gm, $product);
                $gm->setMembership(0);
                $gm->setOfficePart(0);
                
                if ($old->getGrade()->getProductAmount() >= $require->getProductAmount()) {
                    $this->setMessage("take the higher grade than '{$old->getGrade()->getName()}'");
                } else {
                	/**
                	 * @var Office $office
                	 */
                    $office = $request->getAttribute(self::FIELD_OFFICE_ADMIN)->getOffice();
                    $gm->setOffice($office);
                    
                	// --verification de la monais virtuel
                    $product = $gm->getProduct();
                    $membership = (($gm->getMembership()/3)*2);
                    
                    if ($product > $office->getAvailableVirtualMoneyProduct() || $membership >  $office->getAvailableVirualMoneyAfiliate()) {
                        $message = "impossible to perform this operation because the office wallet is insufficient. requered product money: {$product} {$request->getApplication()->getConfig()->get('devise')}, ";
                        $message .= "requered membership money: {$membership} {$request->getApplication()->getConfig()->get('devise')}, ";
                        $message .= "your product wallet: {$office->getAvailableVirtualMoneyProduct()} {$request->getApplication()->getConfig()->get('devise')}";
                        $message .= "your membership wallet: {$office->getAvailableVirualMoneyAfiliate()} {$request->getApplication()->getConfig()->get('devise')}";
                        $this->setMessage($message);
                    }
                	// \\--
                	
                	if (!$this->hasError()) {
	                    $gm->setOld($old);
	                    $gm->setGrade($require);
	                    $gm->setMember($member);
	                    $old->setCloseDate(new \DateTime());
	                    $this->officeDAOManager->load($office);
	                    $this->gradeMemberDAOManager->upgrade($gm);
	                    $this->officeDAOManager->load($office);
                	}
                }                
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
            
        }
        $this->result = $this->hasError()? "failed to upgrade account packages" :"account package upgrade success";
        return $gm;
    }
    
    /**
     * Affiliation d'un membre sur le bonus de re-achat d'un compte X
     * @param Request $request
     * @return GradeMember
     */
    public function affiliateAfterValidation (Request $request) : GradeMember {
        $gm = new GradeMember();
        $grade = $request->getDataPOST(self::FIELD_GRADE);
        
        /**
         * @var Member $sponsor
         * @var MonthlyOrder $monthly
         */
        $sponsor = $request->getAttribute(MemberFormValidator::FIELD_SPONSOR);
        $monthly = $request->getAttribute(MembersController::ATT_MONTHLY_ORDER_FOR_ACCOUNT);
        
        //validation du membre
        $formMember = new MemberFormValidator($this->getDaoManager());
        $member = $formMember->processingMember($request);
        
        //validation de l'adresse du membre
        $formLocalisation = new LocalisationFormValidator($this->getDaoManager());
        $localisation = $formLocalisation->processingLocalisation($request);
        $member->setLocalisation($localisation);
        
        $this->addFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK, $formLocalisation->toFeedback());
        $this->addFeedback(MemberFormValidator::MEMBER_FEEDBACK, $formMember->toFeedback());
        
        $this->processingGrade($gm, $grade);
        $this->processingMember($gm, $member);
        
        $gm->setMember($member);
        
        if ( $member->getSponsor() == null || $member->getSponsor()->getId() != $sponsor->getId()) {
            $this->setMessage("The parent field must not be modified");
        }
        
        if (!$this->hasError()) {
            
            $gm->setMonthlyOrder($monthly);
            $gm->setInitDate(new \DateTime());
            $gm->getMember()->setAdmin($request->getAttribute(self::FIELD_OFFICE_ADMIN));
            $gm->getMember()->setOffice($gm->getMember()->getAdmin()->getOffice());
            $gm->setOffice($gm->getMember()->getAdmin()->getOffice());
            $gm->setGrade($this->gradeDAOManager->findById($gm->getGrade()->getId()));
            $this->processingProduct($gm, $gm->getGrade()->getProductAmount());
            $this->processingMembership($gm, $gm->getGrade()->getMembershipAmount(), $gm->getGrade()->getOfficeAmount());
            
            // --verification de la monais virtuel
            $money = $gm->getGrade()->getMembershipAmount();
            
            if ($money > $member->getOffice()->getAvailableVirualMoneyAfiliate()) {
                $this->setMessage("impossible to perform this operation because the office wallet is insufficient. requered money: {$money} {$request->getApplication()->getConfig()->get('devise')}, your membership wallet: {$member->getOffice()->getAvailableVirualMoneyAfiliate()} {$request->getApplication()->getConfig()->get('devise')}");
            } else if ($gm->getProduct() > $monthly->getAvailable()) {
                $this->setMessage("impossible to perform this operation because the member wallet is insufficient. requered money: {$gm->getProduct()} {$request->getApplication()->getConfig()->get('devise')}, sponsor member walet: {$monthly->getAvailable()} {$request->getApplication()->getConfig()->get('devise')}");
            }
            // \\--
            
            if (!$this->hasError()) {
                try {
                    $this->gradeMemberDAOManager->create($gm);
                    if ($formMember->getProcessPhoto()->isFile()) {
                        $formMember->processingPhoto($member, $formMember->getProcessPhoto(), true, $request->getApplication()->getConfig());
                    }else{
                        $member->setPhoto('img/user.png');
                    }
                    $this->memberDAOManager->updatePhoto($member->getId(), $member->getPhoto());
                } catch (DAOException $e) {
                    $this->setMessage($e->getMessage());
                }
            }
        }
        
        $this->result = $this->hasError()? "Failure to register member" : "successful registration of member";
        
        return $gm;
    }
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     * @return GradeMember
     */
    public function updateAfterValidation(Request $request) {
        throw new PHPBackendException("You not have permission to perfom this operation");
    }

    /**
     * mis en niveau d'un compte, en utilisant le montant deja enregistrer sur la fichhe de vente du compte 
     * d'un membre.
     * lors dela mis en niveau, on peut uniquement metre en niveau son compte, ou un des compte de downline,
     * directement parainer
     *
     * @param Request $request
     * @return GradeMember
     */
    public function pvUpgradeAfterValidation (Request $request) : GradeMember {
        $gm = new GradeMember();

        $grade = $request->getDataPOST(self::FIELD_GRADE);//le packet que demande le membre
        $member = $request->getDataPOST(self::FIELD_MEMBER);//le compte du membre
        
        $this->processingGrade($gm, $grade);
        $this->processingMatriculMember($gm, $member);
        
        if(!$this->hasError()) {
            
            /**
             * le membre proprietaire du compte qui doit etre facturer
             * @var Member $owner
             */
            $owner = $request->getAttribute(self::FIELD_MEMBER);
            
            /**
             * @var MonthlyOrder $monthly
             */
            $monthly = $request->getAttribute(MembersController::ATT_MONTHLY_ORDER_FOR_ACCOUNT);
            $office = $request->getAttribute(self::FIELD_OFFICE_ADMIN)->getOffice();

            $sponsor = $this->memberDAOManager->findSponsor($gm->getMember()->getId());
            $old = $this->gradeMemberDAOManager->findCurrentByMember($gm->getMember()->getId());
                
            /**
             * @var Grade $require
             */
            $require = $this->gradeDAOManager->findById($gm->getGrade()->getId());
            $product = $require->getAmount() - $old->getGrade()->getAmount();
            
            $this->processingProduct($gm, $product);
            $gm->setMembership(0);
            $gm->setOfficePart(0);
            
            if ($old->getGrade()->getAmount() >= $require->getAmount()) {
                $this->setMessage("take the higher grade than '{$old->getGrade()->getName()}'");
            } else {
                
                if($sponsor->getId() != $owner->getId() && $gm->getMember()->getId() != $owner->getId()) {
                    $this->setMessage("Unable to perform this operation because this account does not directly sponsor the account you want to upgrade");
                } else {

                    // --verification de la monais virtuel
                    $product = $gm->getProduct();
                    
                    if ($product > $monthly->getAvailable()) {
                        $message = "impossible to perform this operation because the member wallet is insufficient. requered product money: {$product} {$request->getApplication()->getConfig()->get('devise')}, ";
                        $message .= "requered membership money: 0 {$request->getApplication()->getConfig()->get('devise')}, ";
                        $message .= "your product wallet: {$monthly->getAvailable()} {$request->getApplication()->getConfig()->get('devise')}";
                        $this->setMessage($message);
                    }
                	// \\--

                    $old->setCloseDate(new DateTime());
                    $gm->setInitDate(new DateTime());
                    $gm->setOld($old);
                    $gm->setOffice($office);
                    $gm->setMonthlyOrder($monthly);

                }
                
            }
            
        }
        
        if (!$this->hasError()) {
            try {
                $this->officeDAOManager->load($office);
                $this->gradeMemberDAOManager->upgrade($gm);
                $this->officeDAOManager->load($office);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }

        $this->setResult("operation done successfully", "Failed to execute operation");
        return $gm;
    }
    
    /**
     * activation d'un packet
     * @param Request $request
     * @return GradeMember
     */
    public function enableAfterValidation (Request $request) : GradeMember {
        $gm = new GradeMember();
        $id = $request->getAttribute(self::CHAMP_ID);
        
        $this->traitementId($gm, $id);
        
        if (!$this->hasError()) {
            try {
                /**
                 * @var GradeMember $inDatabase
                 */
                $inDatabase = $this->gradeMemberDAOManager->findById(intval($id), 10);
                $inDatabase->setInitDate(new \DateTime());
                $this->gradeMemberDAOManager->enable($inDatabase);
                $gm = $inDatabase;
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "failure to activate account rank":"successful activation of the account rank";
        
        return $gm;
    }

}

