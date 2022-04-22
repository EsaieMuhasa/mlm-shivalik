<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\User;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use PHPBackend\Request;
use PHPBackend\Config\VarList;
use PHPBackend\Dao\DAOException;
use PHPBackend\File\UploadedFile;
use PHPBackend\Validator\IllegalFormValueException;
use Core\Shivalik\Managers\GradeMemberDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
class MemberFormValidator extends UserFormValidator
{
    const FIELD_PARENT = 'parent';
    const FIELD_SPONSOR = 'sponsor';
    const FIELD_FOOT = 'foot';
    const DEFINE_CONFIG_FOOTS = 'footsMember';
    const MEMBER_FEEDBACK = 'memberFeedback';
    const FIELD_CHILD = 'child';
    const FIELD_GRADE =  'grade';
    const FIELD_MATRICUL = 'matricul';
    
    const RGX_MATRICUL = '#^[A-Z]([0-9]{1,7})#';
    
    /**
     * la photo encours de traitement
     * @var UploadedFile
     */
    private $processPhoto;
    
    /**
     * @var GradeDAOManager
     */
    private $gradeDAOManager;
    
    /**
     * @var GradeMemberDAOManager
     */
    private $gradeMemberDAOManager;
    
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    /**
     * validation du parent d'un du membre adhereant
     * si le parent est null, on laisse passer. sinon on verifie si le parent est dans la BDD
     * @param string $parent le matricule du parent
     * @throws IllegalFormValueException
     */
    private function validationParent ($parent) : void {
        try {
            if ($parent!=null && !$this->memberDAOManager->checkByMatricule($parent)) {
                throw new IllegalFormValueException("unknown ID in system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * validation du sponsor du membre
     * @param string $sponsor le matricule du sponsor
     * @throws IllegalFormValueException
     */
    private function validationSponsor ($sponsor) : void {
        
        if ($sponsor == null) {
            throw new IllegalFormValueException("sponsor can not be empty");
        }
        
        try {
            if (!$this->memberDAOManager->checkByMatricule($sponsor)) {
                throw new IllegalFormValueException("unknown ID in system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Validation de l'Id du membre
     * @param string $matricul
     * @throws IllegalFormValueException
     */
    private function validationMatricul ($matricul) : void {
        if($matricul == null) {
            throw new IllegalFormValueException("Please enter the member ID");
        } else if (!preg_match(self::RGX_MATRICUL, $matricul)) {
            throw new IllegalFormValueException("Enter member id in valid format");
        } else {
            try {
                if (!$this->memberDAOManager->checkByMatricule($matricul)) {
                    throw new IllegalFormValueException("unknown member ID in database");
                }
            } catch (DAOException $e) {
                throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
    
    /**
     * finalisation dela validation du pseudo de connexion d'un membre
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::validationPseudo()
     */
    protected function validationPseudo($pseudo, bool $onConnection = false, $id = null): void
    {
        parent::validationPseudo($pseudo, $onConnection, $id);
        try {    
            if ($onConnection) {
                if (!$this->memberDAOManager->checkByPseudo($pseudo)) {
                    throw new IllegalFormValueException("unknown user in system");
                }
            } else {                
                if ($this->memberDAOManager->checkByPseudo($pseudo, $id)) {
                    throw new IllegalFormValueException("this username are used");
                }
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
        
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::validationTelephone()
     */
    protected function validationTelephone($telephone, $id = null): void
    {
        parent::validationTelephone($telephone, $id);
        try {
            if($telephone!= null && $this->memberDAOManager->checkByTelephone($telephone, $id)){
                throw new IllegalFormValueException("This telephone number are used");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::validationEmail()
     */
    protected function validationEmail($email, $id = - 1): void
    {
        parent::validationEmail($email, $id);
        try {
            if($email != null && $this->memberDAOManager->checkByEmail($email, $id)){
                throw new IllegalFormValueException("This mail are used");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage());
        }
    }

    /**
     * validation du peid sur le quel le membre doit doit etre affecter
     * @param int $foot
     * @param VarList $foots
     * @throws IllegalFormValueException
     */
    private function validationFoot ($foot, VarList $foots) : void {
        
        if ($foot == null) {
            throw new IllegalFormValueException("the assignment footer is mandatory");
        }
        
        foreach ($foots->getItems() as $item) {
            if ($item->getValue() == $foot) {
                return;
            }
        }
        
        throw new IllegalFormValueException("the chosen allocation foot is unknown in the system configuration");
    }
    
    /**
     * Validation d'un user.
     * cette methode prend son sens lors de l'insertion d'un membre comme parent
     * du $child.
     * @param string $child
     */
    private function validationChild ($child) : void {
        if ($child == null) {
            throw new IllegalFormValueException("Child ID cannot be empty");
        } elseif (!is_string($child)) {
            throw new IllegalFormValueException("Member ID must be string type");
        }
        
        try {
            if (!$this->memberDAOManager->checkByMatricule($child)) {
                throw new IllegalFormValueException("Child ID unknown in system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Validation du packet d'un membre.
     * cette methode est utiliser dans le processuce de validation, lors de l'insersion
     * d'un membre dans l'arbre genealogique
     * @param string|int $grade
     * @throws IllegalFormValueException
     */
    private function validationGrade ($grade) : void {
        if ($grade == null) {
            throw new IllegalFormValueException("packet ID cannot null or empty");
        } else if (!preg_match(self::RGX_INT_POSITIF, $grade)) {
            throw new IllegalFormValueException("Packet reference must be integer");
        } 
        
        try {
            if (!$this->gradeDAOManager->checkById($grade)) {
                throw new IllegalFormValueException("the package indexed by {$grade} does not exist in database");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Processuce de traitement du compte enfant,
     * pour lequel on veut changer de parent
     * @param Account $account
     * @param string $child
     */
    private function processingChild (Account $account, $child) : void {
        try {
            $this->validationChild($child);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_CHILD, $e->getMessage());
        }
    }
    
    private function processingGrad (Account $account, $grade) : void {
        try {
            $this->validationGrade($grade);
            if ($account->getMember()->getPacket() == null) {
                $account->getMember()->setPacket(new GradeMember());
            }
            $account->getMember()->getPacket()->setGrade($this->gradeDAOManager->findById(intval($grade, 10)));
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_GRADE, $e->getMessage());
        }
    }
    
    /**
     * processuce de traitement/validation du parent du membre adherant
     * @param Member $member
     * @param int $parent
     */
    private function processingParent (Member $member, $parent, VarList $foots) : void {
        try {
            $this->validationParent($parent);
            if ($parent != null) {
                $member->setParent($this->memberDAOManager->findByMatricule($parent));
            }
            
            if ($member->getSponsor() === null) {
                return;
            }
            
            /**
             * @var Member $parentNode
             */
            $parentNode = $member->getParent() !== null? $member->getParent() : $member->getSponsor();
            $foot = null;

            foreach ($foots->getItems() as $item) {//verification des pieds du parent
                if (!$this->memberDAOManager->checkChild($parentNode->getId(), intval($item->getValue()))) {
                    $foot = intval($item->getValue(), 10);
                    break;
                }
            }
            
            if ($foot === null) {
                
                while ($this->memberDAOManager->checkChilds($parentNode->getId())) {
                    $childs =$this->memberDAOManager->findChilds($parentNode->getId());
                    
                    foreach ($childs as $child) {//verification des pieds de certains affants du reseau
                        foreach ($foots->getItems() as $item) {
                            if (!$this->memberDAOManager->checkChild($child->getId(), intval($item->getValue()))) {
                                $foot = intval($item->getValue(), 10);
                                $parentNode = $child;
                                break;
                            }
                        }
                        
                        if ($parentNode == $child) {
                            break;
                        }
                    }
                    
                    if ($foot !== null) {
                        break;
                    }
                    
                    $parentNode = $childs[array_key_first($childs)];
                }
            }
            
            $member->setFoot($foot);
            $member->setParent($parentNode);
            
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PARENT, $e->getMessage());
            $member->setParent(new Member(array('matricule' => $parent)));
        }
    }
    
    /**
     * processuce de traitement/validation du sponsor de membre
     * @param Member $member
     * @param string $sponsor le matricule du sponsor
     */
    private function processingSponsor (Member $member, $sponsor) : void {
        try {
            $this->validationSponsor($sponsor);
            if ($sponsor!=null) {
                $member->setSponsor($this->memberDAOManager->findByMatricule($sponsor));
            }
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_SPONSOR, $e->getMessage());
        }
    }
    
    /**
     * processuce de validation/traitement du matricule
     * @param Member $member
     * @param string $matricul
     */
    private function processingMatricul (Member $member, $matricul) : void {
        try {
            $this->validationMatricul($matricul);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_MATRICUL, $e->getMessage());
        }
        
        $member->setMatricule($matricul);
    }
    
    /**
     * @return UploadedFile|NULL
     */
    public function getProcessPhoto() :?UploadedFile
    {
        return $this->processPhoto;
    }

    /**
     * 
     * @param Request $request
     * @return Member
     */
    public function processingMember (Request $request) : Member {
        $user = new Member();
        $name = $request->getDataPOST(self::FIELD_NAME);
        $postName = $request->getDataPOST(self::FIELD_POST_NAME);
        $lastName = $request->getDataPOST(self::FIELD_LAST_NAME);
        $email = $request->getDataPOST(self::FIELD_EMAIL);
        $telephone = $request->getDataPOST(self::FIELD_TELEPHONE);
        $pseudo = $request->getDataPOST(self::FIELD_PSEUDO);
        $password = $request->getDataPOST(self::FIELD_PASSWORD);
        $confirmation = $request->getDataPOST(self::FIELD_CONFIRMATION);
        $parent = $request->getDataPOST(self::FIELD_PARENT);
        $sponsor = $request->getDataPOST(self::FIELD_SPONSOR);
        
        $photo = $request->getUploadedFile(self::FIELD_PHOTO);
        $user->setKind($request->getDataPOST(self::FIELD_KIND));
        
        $this->processingName($user, $name);
        $this->processingPostName($user, $postName);
        $this->processingLastName($user, $lastName);
        $this->processingEmail($user, $email);
        $this->processingTelephone($user, $telephone);
        $this->processingPassword($user, $password, $confirmation, false);
        $this->processingPseudo($user, $pseudo);
        if ($photo->isFile()) {
	        $this->processingPhoto($user, $photo);
        }
        $this->processingSponsor($user, $sponsor);
        $this->processingParent($user, $parent, $request->getApplication()->getConfig()->get(self::DEFINE_CONFIG_FOOTS));
        $this->processPhoto = $photo;

        return $user;
    }
    
    /**
     * reinitialisation du mot de passe d'un membre par l'administrateur cetrale
     * @param Request $request
     * @return Member
     */
    public function resetPasswordAfterValidation (Request $request) : Member {
    	$member = new Member();
    	$password = $request->getDataPOST(self::FIELD_PASSWORD);
    	$confirmation = $request->getDataPOST(self::FIELD_CONFIRMATION);
    	
    	$this->processingPassword($member, $password, $confirmation);
    	
    	if (!$this->hasError()) {
    		try {
    			$id = $request->getAttribute(self::CHAMP_ID);
    			$this->memberDAOManager->updatePassword($id, $member->getPassword());
    		} catch (DAOException $e) {
    			$this->setMessage($e->getMessage());
    		}
    	}
    	
    	$this->result = $this->hasError()? "password update failed":"password update success";
    	
    	return $member;    
    }
    
    /**
     * mise en jour du mot de passe du compte d'un membre, par le membre lui meme
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::updatePasswordAfterValidation()
     */
    public function updatePasswordAfterValidation(Request $request): User
    {
        $member = new Member();
        $old = $request->getDataPOST('old');
        $password = $request->getDataPOST(self::FIELD_PASSWORD);
        $confirmation = $request->getDataPOST(self::FIELD_CONFIRMATION);
        
        $this->processingPassword($member, $password, $confirmation);
        
        if ($old == null) {
            $this->addError('old', "Your old password is required");
        }
        
        if (!$this->hasError()) {
            try {
                $user = $this->memberDAOManager->findById($request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION)->getId());
                if ($user->getPassword() != sha1($old)) {
                    $this->addError('old', "invalid password");
                }else {
                    $this->memberDAOManager->updatePassword($user->getId(), $member->getPassword());
                }
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "password update failed" : "password update success";
        return $member;
    }
    
    /**
     * modification de la photo de profil par le membre lui meme
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::updatePhotoAfterValidation()
     */
    public function updatePhotoAfterValidation(Request $request): User
    {
        $member = new Member();
        $photo = $request->getUploadedFile(self::FIELD_PHOTO);
        
        if (!$photo->isImage()) {
            $this->addError(self::FIELD_PHOTO, "make sure you have selected a photo on your terminal");
        }
        /**
         * le membre actuelement connecter
         * @var Member $inSession
         */
        $inSession = $request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        $member->setId($inSession->getId());
        $this->processingPhoto($member, $photo);
        
        if (!$this->hasError()) {
            try {
                $this->processingPhoto($member, $photo, true, $request->getApplication()->getConfig());
                $this->memberDAOManager->updatePhoto($inSession->getId(), $member->getPhoto());
                $inSession->setPhoto($member->getPhoto());
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "failed to update profile picture" : "profile picture update success";
        return $member;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     */
    public function createAfterValidation(Request $request)
    {
        $user = $this->processingMember($request);
        $photo = $request->getUploadedFile(self::FIELD_PHOTO);
        $this->processingPhoto($user, $photo);
       
        if (!$this->hasError()) {
            try {
                $this->processingPhoto($user, $photo, true, $request->getApplication()->getConfig());
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        $this->result = $this->hasError()? "failure to register member" : "member registration success";
        
        return $user; 
    }
    
    /**
     * Insersion d'un nouveau membre dans l'arbre genealogique.
     * Le compte du membre est directement considere comme compte parent
     * du compte où on prefere l'inserer
     * @param Request $request
     * @return Account
     */
    public function insertBelowAferValidation (Request $request) : Account {
        $user = new Member();
        $account = new Account($user);
        
        $name = $request->getDataPOST(self::FIELD_NAME);
        $postName = $request->getDataPOST(self::FIELD_POST_NAME);
        $lastName = $request->getDataPOST(self::FIELD_LAST_NAME);
        $email = $request->getDataPOST(self::FIELD_EMAIL);
        $telephone = $request->getDataPOST(self::FIELD_TELEPHONE);
        $pseudo = $request->getDataPOST(self::FIELD_PSEUDO);
        $password = $request->getDataPOST(self::FIELD_PASSWORD);
        $confirmation = $request->getDataPOST(self::FIELD_CONFIRMATION);
        $grade = $request->getDataPOST(self::FIELD_GRADE);
        
        $child = $request->getDataPOST(self::FIELD_CHILD);
        
        $photo = $request->getUploadedFile(self::FIELD_PHOTO);
        $user->setKind($request->getDataPOST(self::FIELD_KIND));
        
        $this->processingName($user, $name);
        $this->processingPostName($user, $postName);
        $this->processingLastName($user, $lastName);
        $this->processingEmail($user, $email);
        $this->processingTelephone($user, $telephone);
        $this->processingPassword($user, $password, $confirmation);
        $this->processingPseudo($user, $pseudo);
        $this->processingChild($account, $child);
        $this->processingGrad($account, $grade);
        
        if ($photo->isFile()) {
            $this->processingPhoto($user, $photo);
        }
        
        //validation de l'adresse du membre
        $formLocalisation = new LocalisationFormValidator($this->getDaoManager());
        $localisation = $formLocalisation->processingLocalisation($request);
        $user->setLocalisation($localisation);
        $this->addFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK, $formLocalisation->toFeedback());
        
        if (!$this->hasError()) {
            try {
                $node = $this->memberDAOManager->findByMatricule($child);
                $node->setPacket($this->gradeMemberDAOManager->findCurrentByMember($node->getId()));
                $childAccount = $this->memberDAOManager->loadAccount($node, false);
                
                /**
                 * @var Office[] $offices
                 * @var Office $office
                 */
                $offices =  $this->officeDAOManager->findAll();
                
                $now = new \DateTime();
                
                $g = $account->getMember()->getPacket()->getGrade();
                $gm = $account->getMember()->getPacket();
                
                $gm->setMembership(20);
                $gm->setOfficePart(10);
                $gm->setProduct($g->getAmount() - ($gm->getMembership() + $gm->getOfficePart()));
                $gm->setInitDate($now);
                $gm->setDateAjout($now);
                
                foreach ($offices as $office) {//recherche d'un bureau centrale
                    if ($office->isCentral()) {
                        $admin = $this->officeAdminDAOManager->findActiveByOffice($office->getId());
                        $user->setOffice($office);
                        $user->setAdmin($admin);
                        $gm->setOffice($office);
                        break;
                    }
                }
                
                $this->memberDAOManager->insertBelow($account, $childAccount);
                
                if ($photo->isFile()) {
                    $this->processingPhoto($user, $photo, true, $request->getApplication()->getConfig());
                } else {
                    $user->setPhoto('img/user.png');
                }
                $this->memberDAOManager->updatePhoto($user->getId(), $user->getPhoto());
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->setResult("account insertion success below {$child}", "failed to insert account below {$child}");
        return $account;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     * @return Member
     */
    public function updateAfterValidation(Request $request)
    {
    	$user = new Member();
    	$id = $request->getAttribute(self::CHAMP_ID);
    	$user->setId($id);
    	
    	$name = $request->getDataPOST(self::FIELD_NAME);
    	$postName = $request->getDataPOST(self::FIELD_POST_NAME);
    	$lastName = $request->getDataPOST(self::FIELD_LAST_NAME);
    	$email = $request->getDataPOST(self::FIELD_EMAIL);
    	$telephone = $request->getDataPOST(self::FIELD_TELEPHONE);
    	$pseudo = $request->getDataPOST(self::FIELD_PSEUDO);
    	
    	$user->setKind($request->getDataPOST(self::FIELD_KIND));
    	
    	$this->processingName($user, $name);
    	$this->processingPostName($user, $postName);
    	$this->processingLastName($user, $lastName);
    	$this->processingEmail($user, $email, $id);
    	$this->processingTelephone($user, $telephone, $id);
    	$this->processingPseudo($user, $pseudo, false, $id);

    	try {
    		$this->memberDAOManager->update($user, $id);
    	} catch (DAOException $e) {
    		$this->setMessage($e->getMessage());
    	}
    	$this->result = $this->hasError()? "update failure":"";
        return $user;
    }
    
    /**
     * Recherche de la personne proprietaire de l'ID qui ce trouve dans le POST
     * de la requette
     * @param Request $request
     * @return Member
     */
    public function searchByIdAfterValidation (Request $request) : Member {
        $member = new Member();
        
        $matricul = $request->getDataPOST(self::FIELD_MATRICUL);
        $this->processingMatricul($member, $matricul);
        
        if (!$this->hasError()) {
            try {
                $member = $this->memberDAOManager->findByMatricule($matricul);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->setResult("{$member->getNames()}", "Query execution failed");
        return $member;
    }
    
    /**
     * Pour effectuer une recherche
     * @param Request $request
     * @return Member[]
     */
    public function searchAfterValidation (Request $request) {
        $searchData = [];
        $value = $request->existInPOST('index')? $request->getDataPOST("index") : $request->getDataGET('index');
        $count = 0;
        if ($value == null || empty($value)) {
            $this->setMessage("no results match the search index");
        } else {
            $index = explode(" ", $value);
            $selectedIndex = [];
            foreach ($index as $item) {
                if (strlen($item) >= 3) {
                    $selectedIndex[] = $item;
                }
            }
            try {
                $searchData = $this->memberDAOManager->search(count($selectedIndex) != 0? $selectedIndex : $value);
                $count = count($searchData);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "Failed to fulfill request" : "{$count} search result for indexes '{$value}'";
        return  $searchData;
    }

}

