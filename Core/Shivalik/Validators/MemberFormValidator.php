<?php
namespace Core\Shivalik\Validators;

use Library\Config\VarList;
use Library\IllegalFormValueException;
use Library\DAOException;
use Library\HTTPRequest;
use Library\File;
use Applications\Member\MemberApplication;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\User;

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
    
    /**
     * la photo encours de traitement
     * @var File
     */
    private $processPhoto;
    
    
    private function validationParent ($parent) : void {
        try {
            if ($parent!=null && !$this->memberDAOManager->matriculeExist($parent)) {
                throw new IllegalFormValueException("unknown ID in system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    private function validationSponsor ($sponsor) : void {
        try {
            if ($sponsor!=null && !$this->memberDAOManager->matriculeExist($sponsor)) {
                throw new IllegalFormValueException("unknown ID in system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::validationPseudo()
     */
    protected function validationPseudo($pseudo, bool $onConnection = false, $id = -1): void
    {
        parent::validationPseudo($pseudo, $onConnection, $id);
        try {    
            if ($onConnection) {
                if (!$this->memberDAOManager->pseudoExist($pseudo)) {
                    throw new IllegalFormValueException("unknown user");
                }
            } else {                
                if ($this->memberDAOManager->pseudoExist($pseudo, $id)) {
                    throw new IllegalFormValueException("username are used");
                }
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
        
    }

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
       
    private function processingParent (Member $member, $parent) : void {
        try {
            $this->validationParent($parent);
            if ($parent != null) {
                $member->setParent($this->memberDAOManager->getForMatricule($parent));
            }
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PARENT, $e->getMessage());
            $member->setParent(new Member(array('matricule' => $parent)));
        }
    }
    
    private function processingSponsor (Member $member, $sponsor) : void {
        try {
            $this->validationSponsor($sponsor);
            if ($sponsor!=null) {
                $member->setSponsor($this->memberDAOManager->getForMatricule($sponsor));
            }
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_SPONSOR, $e->getMessage());
            $member->setSponsor(new Member(array('matricule' => $sponsor)));
        }
    }
    
    private function processingFoot (Member $member, VarList $foots) : void {
        $foot = -1;
        try {
            if ($member->getParent() != null && $member->getParent()->getId()!=null && $member->getParent()->getId()>0) {
                $parent = $member->getParent();
                
                foreach ($foots->getItems() as $item) {
                    if (!$this->memberDAOManager->hasChild($parent->getId(), intval($item->getValue()))) {
                        $foot = intval($item->getValue());
                    }
                }
                
            }else {
                $member->setFoot(null);
                return;
            }
        } catch (DAOException $e) {
            $this->addError(self::FIELD_FOOT, $e->getMessage());
        }
        
        if ($foot == -1) {
            $this->addError(self::FIELD_FOOT, "All the parents' feet are already accupied");
        }
        
        $member->setFoot($foot);
    }
    
    /**
     * @return \Library\File
     */
    public function getProcessPhoto() :?File
    {
        return $this->processPhoto;
    }

    /**
     * 
     * @param HTTPRequest $request
     * @return Member
     */
    public function processingMember (HTTPRequest $request) : Member {
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
        
        $photo = $request->getFile(self::FIELD_PHOTO);
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
        $this->processingParent($user, $parent);
        $this->processingSponsor($user, $sponsor);
        $this->processingFoot($user, $request->getApplication()->getConfig()->get(self::DEFINE_CONFIG_FOOTS));
        $this->processPhoto = $photo;

        return $user; 
    }
    
    /**
     * @return Member
     */
    public function resetPasswordAfterValidation (HTTPRequest $request) : Member {
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
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::updatePasswordAfterValidation()
     */
    public function updatePasswordAfterValidation(\Library\HTTPRequest $request): User
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
                $user = $this->memberDAOManager->getForId(MemberApplication::getConnectedMember()->getId());
                if ($user->getPassword() != sha1($old)) {
                    $this->addError('old', "invalid password");
                }else {
                    $this->memberDAOManager->updatePassword($user->getId(), $member->getPassword());
                }
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "password update failed":"password update success";
        
        return $member;        
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::updatePhotoAfterValidation()
     */
    public function updatePhotoAfterValidation(\Library\HTTPRequest $request): User
    {
        $member = new Member();
        $photo = $request->getFile(self::FIELD_PHOTO);
        
        if (!$photo->isFile()) {
            $this->addError(self::FIELD_PHOTO, "make sure you have selected a photo on your terminal");
        }
        
        $member->setId(MemberApplication::getConnectedMember()->getId());
        $this->processingPhoto($member, $photo);
        
        if (!$this->hasError()) {
            try {
                $this->processingPhoto($member, $photo, true);
                $this->memberDAOManager->updatePhoto(MemberApplication::getConnectedMember()->getId(), $member->getPhoto());
                MemberApplication::getConnectedMember()->setPhoto($member->getPhoto());
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "failed to update profile picture" : "profile picture update success";
        
        return $member;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::createAfterValidation()
     */
    public function createAfterValidation(\Library\HTTPRequest $request)
    {
        $user = $this->processingMember($request);
        $photo = $request->getFile(self::FIELD_PHOTO);
        $this->processingPhoto($user, $photo);
       
        if (!$this->hasError()) {
            try {
                $this->processingPhoto($user, $photo, true);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        $this->result = $this->hasError()? "failure to register member" : "member registration success";
        
        return $user; 
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::deleteAfterValidation()
     */
    public function deleteAfterValidation(\Library\HTTPRequest $request)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::recycleAfterValidation()
     */
    public function recycleAfterValidation(\Library\HTTPRequest $request)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::removeAfterValidation()
     */
    public function removeAfterValidation(\Library\HTTPRequest $request)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::updateAfterValidation()
     * @return Member
     */
    public function updateAfterValidation(\Library\HTTPRequest $request)
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

}

