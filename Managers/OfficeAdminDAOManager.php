<?php
namespace Managers;

use Entities\OfficeAdmin;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class OfficeAdminDAOManager extends AbstractUserDAOManager
{
    /**
     * @var OfficeDAOManager
     */
    protected $officeDAOManager;
    
    /**
     * @param int $officeId
     * @param bool $active
     * @return bool
     */
    public abstract function hasAdmin (int $officeId, bool $active = true) : bool ;
    
    /**
     * revoie le compte administrateur active d'un office
     * @param int $officeId identifiant de l'office
     * @return OfficeAdmin
     */
    public abstract function activeInOffice (int $officeId) : OfficeAdmin;
    
    /**
     * renvoie le compte de l'administrateur d'un office
     * N.B: le dernier compte enregistrer
     * @param int $officeId
     * @return OfficeAdmin
     */
    public abstract function getAdmin (int $officeId) : OfficeAdmin ;
    
    /**
     * {@inheritDoc}
     * @see \Managers\AbstractUserDAOManager::getForEmail()
     * @return OfficeAdmin
     */
    public function getForEmail(string $email, bool $forward = false)
    {
        /**
         * @var \Entities\OfficeAdmin $user
         */
        $user = parent::getForEmail($email, $forward);
        if($forward) $user->setOffice($this->officeDAOManager->getForId($user->getOffice()->getId()));
        return $user;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\AbstractUserDAOManager::getForId()
     */
    public function getForId(int $id, bool $forward = true)
    {
        /**
         * @var \Entities\OfficeAdmin $user
         */
        $user = parent::getForId($id, $forward);
        if($forward) $user->setOffice($this->officeDAOManager->getForId($user->getOffice()->getId()));
        return $user;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\AbstractUserDAOManager::getForTelephone()
     */
    public function getForTelephone(string $telephone, bool $forward = false)
    {
        /**
         * @var \Entities\OfficeAdmin $user
         */
        $user = parent::getForTelephone($telephone, $forward);
        if($forward) $user->setOffice($this->officeDAOManager->getForId($user->getOffice()->getId()));
        return $user;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getAll()
     */
    public function getAll($limit = -1, $offset = -1)
    {
        $users = parent::getAll($limit, $offset);
        foreach ($users as $user){
            $user->setOffice($this->officeDAOManager->getForId($user->getOffice()->getId()));
        }
        return $users;
    }
  
}

