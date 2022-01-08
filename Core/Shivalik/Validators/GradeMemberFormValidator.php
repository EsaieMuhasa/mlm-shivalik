<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Request;
use PHPBackend\Http\HTTPRequest;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

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
     * @param string $grade
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
     * {@inheritDoc}
     * @see \PHPBackend\Validator\DefaultFormValidator::validationId()
     */
    protected function validationId($id): void
    {
        parent::validationId($id);
        
        try {
            if (!$this->gradeMemberDAOManager->checkById(intval($id, 10))) {
                throw new DAOException("unknown user rank in the system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

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
    
    private function validationMembership ($membership) : void {
        if ($membership == null) {
            throw new IllegalFormValueException("the membership fee is mandatory");
        }elseif (!preg_match(self::RGX_NUMERIC_POSITIF, $membership)){
            throw new IllegalFormValueException("the membership amount must be a positive numeric value");
        }
    }
    
    private function validationProduct ($product) : void {
        if ($product == null) {
            throw new IllegalFormValueException("the amount allocated to the products is mandatory");
        }elseif (!preg_match(self::RGX_NUMERIC_POSITIF, $product)){
            throw new IllegalFormValueException("the amount allocated to the products must be a positive numerical value");
        }
    }
    
    private function processingGrade (GradeMember $gm, $grade) : void {
        try {
            $this->validationGrade($grade);
            $gm->setGrade($grade);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_GRADE, $e->getMessage());
        }
    }
    
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
    public function createAfterValidation(Request $request)
    {
        $gm = new GradeMember();
        $grade = $request->getDataPOST(self::FIELD_GRADE);
        
        $formMember = new MemberFormValidator($this->getDaoManager());
        $member = $formMember->processingMember($request);
        
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
            $this->processingProduct($gm, $gm->getGrade()->getAmount()-30);
            $this->processingMembership($gm, 20, 10);
            
            // --verification de la monais virtuel
            $money = $gm->getProduct() + (($gm->getMembership()/3)*2);
            if ($money > $member->getOffice()->getAvailableVirtualMoney()) {
            	$this->setMessage("impossible to perform this operation because the office wallet is insufficient. requered money: {$money} {$request->getApplication()->getConfig()->get('devise')}, your walet: {$member->getOffice()->getAvailableVirtualMoney()} {$request->getApplication()->getConfig()->get('devise')}");
            }
            // \\--
            
            if (!$this->hasError()) {
                try {
                    $this->gradeMemberDAOManager->create($gm);
                    if ($formMember->getProcessPhoto()->isFile()) {
                        $formMember->processingPhoto($member, $formMember->getProcessPhoto(), true);
                    }else{
                        $member->setPhoto('img/user.png');
                    }
                    $this->memberDAOManager->updatePhoto($member->getId(), $member->getPhoto());
                } catch (DAOException $e) {
                    $this->setMessage($e->getMessage());
                }
            }
        }
        
        $this->result = $this->hasError()? "failure to register member" : "successful registration of member";
        
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
                $old = $this->gradeMemberDAOManager->getCurrent($member->getId());
                
                $require = $this->gradeDAOManager->findById($gradeId);
                
                $product = $require->getAmount() - $old->getGrade()->getAmount();
                
                $this->processingProduct($gm, $product);
                $gm->setMembership(0);
                $gm->setOfficePart(0);
                
                if ($old->getGrade()->getAmount() >= $require->getAmount()) {
                    $this->setMessage("take the higher grade than '{$old->getGrade()->getName()}'");
                } else {
                	
                    $gm->setOffice($request->getAttribute(self::FIELD_OFFICE_ADMIN)->getOffice());
                    
                	// --verification de la monais virtuel
                	$money = $gm->getProduct() + $gm->getMembership();
                	if ($money > $gm->getOffice()->getAvailableVirtualMoney()) {
                		$this->setMessage("impossible to perform this operation because the office wallet is insufficient. requered money: {$money} {$request->getApplication()->getConfig()->get('devise')}, your walet: {$gm->getOffice()->getAvailableVirtualMoney()} {$request->getApplication()->getConfig()->get('devise')}");
                	}
                	// \\--
                	
                	if (!$this->hasError()) {
	                    $gm->setOld($old);
	                    $gm->setGrade($require);
	                    $gm->setMember($member);
	                    $old->setCloseDate(new \DateTime());
	                    $this->gradeMemberDAOManager->upgrade($gm);
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
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     * @return GradeMember
     */
    public function updateAfterValidation(Request $request)
    {
        $gm = new GradeMember();
        $id = $request->getDataGET(self::CHAMP_ID);
        $grade = $request->getDataPOST(self::FIELD_GRADE);
        $member = $request->getDataPOST(self::FIELD_MEMBER);
        $membership = $request->getDataPOST(self::FIELD_MEMBERSHIP);
        $product = $request->getDataPOST(self::FIELD_PRODUCT);
        
        $this->processingProduct($gm, $product);
        $this->processingMembership($gm, $membership);
        $this->processingGrade($gm, $grade);
        $this->processingMember($gm, $member);
        $this->traitementId($gm, $id);
        
        if (!$this->hasError()) {
            try {
                
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "failure to register changes to member's rank" : "successful registration of member rank changes";
        
        return $gm;
    }
    
    /**
     * @param HTTPRequest $request
     * @return GradeMember
     */
    public function enableAfterValidation (HTTPRequest $request) : GradeMember {
        $gm = new GradeMember();
        $id = $request->getAttribute(self::CHAMP_ID);
        
        $this->traitementId($gm, $id);
        
        if (!$this->hasError()) {
            try {
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

