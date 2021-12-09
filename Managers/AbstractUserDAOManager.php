<?php
namespace Managers;

use Library\AbstractDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AbstractUserDAOManager extends AbstractDAOManager
{
    
    /**
     * @var LocalisationDAOManager
     */
    protected $localisationDAOManager;
    
    /**
     * udpate
     * @param int $id
     * @param bool $enable
     */
    public function updateState (int $id, bool $enable) : void {
        $this->pdo_updateInTable($this->getTableName(), 
            array('enable' => ($enable? 1:'0')), $id, false);
    }

    /**
     * 
     * @param string $telephone
     * @param int $id
     * @return bool
     */
    public function telephoneExist (string $telephone, int $id = -1) : bool {
        return $this->columnValueExist('telephone', $telephone, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getForId()
     */
    public function getForId(int $id, bool $forward = true)
    {
        $user = parent::getForId($id, $forward);
        if ($user->getLocalisation() != null) {
            $user->setLocalisation($this->localisationDAOManager->getForId($user->getLocalisation()->getId()));
        }
        return $user;
    }

    /**
     * 
     * @param string $pseudo
     * @param int $id
     * @return bool
     */
    public function pseudoExist (string $pseudo, int $id = -1) : bool {
        return $this->columnValueExist('pseudo', $pseudo, $id);
    }
    
    
    /**
     * 
     * @param string $email
     * @param int $id
     * @return bool
     */
    public function emailExist (string $email, int $id = -1) : bool {
        return $this->columnValueExist('email', $email, $id);
    }
    
    /**
     * 
     * @param string $pseudo
     * @param bool $forward
     * @return \Library\DBEntity
     */
    public function getForPseudo (string $pseudo, bool $forward = false) {
        /**
         * @var \Entities\User $user
         */
        $user = $this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'pseudo', $pseudo);
        if ($user->getLocalisation() != null) {
            $user->setLocalisation($this->localisationDAOManager->getForId($user->getLocalisation()->getId()));
        }
        
        return $user;
    }
    
    
    /**
     * 
     * @param string $email
     * @param bool $forward
     * @return \Library\DBEntity
     */
    public function getForEmail (string $email, bool $forward = false) {
        $user = $this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'email', $email);
        if ($user->getLocalisation() != null) {
            $user->setLocalisation($this->localisationDAOManager->getForId($user->getLocalisation()->getId()));
        }
        return $user;
    }
    
    
    /**
     * 
     * @param string $telephone
     * @param bool $forward
     * @return \Library\DBEntity
     */
    public function getForTelephone (string $telephone, bool $forward = false) {
        $user = $this->pdo_uniqueFromTableColumnValue($this->getTableName(), $this->getMetadata()->getName(), 'telephone', $telephone);
        if ($user->getLocalisation() != null) {
            $user->setLocalisation($this->localisationDAOManager->getForId($user->getLocalisation()->getId()));
        }
        return $user;
    }
    
    /**
     * 
     * @param int $userId
     * @param string $photo
     */
    public function updatePhoto (int $userId, string $photo) : void {
        $this->pdo_updateInTable($this->getTableName(), array('photo' => $photo), $userId, false);
    }
    
    
    /**
     * 
     * @param int $userId
     * @param string $password
     */
    public function updatePassword (int $userId, string $password) : void {
        $this->pdo_updateInTable($this->getTableName(), array('password' => $password), $userId, false);
    }
}

