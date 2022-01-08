<?php
namespace Core\Shivalik\Managers;


use Core\Shivalik\Entities\OfficeAdmin;

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
     * exist-il aumoin un compte admin pour l'office dont l'ID est en premier paramtre?
     * @param int $officeId
     * @param bool $active
     * @return bool
     */
    public abstract function checkByOffice (int $officeId, bool $active = true) : bool ;
    
    /**
     * renvoie le compte administrateur active d'un office
     * @param int $officeId identifiant de l'office
     * @return OfficeAdmin
     */
    public abstract function findActiveByOffice (int $officeId) : OfficeAdmin;
    
    /**
     * renvoie le compte de l'administrateur d'un office
     * N.B: le dernier compte enregistrer
     * @param int $officeId
     * @return OfficeAdmin
     */
    public abstract function findAdminByOffice (int $officeId) : OfficeAdmin ;

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\AbstractUserDAOManager::findByColumnName()
     */
    public function findByColumnName(string $columnName, $value, bool $forward = true)
    {
        $admin = parent::findByColumnName($columnName, $value, $forward);
        if($forward) {
            $admin->setOffice($this->officeDAOManager->findById($admin->getOffice()->getId()));
        }
        return $admin;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findAll()
     */
    public function findAll(?int $limit = null, int $offset = 0)
    {
        $users = parent::findAll($limit, $offset);
        foreach ($users as $user){
            $user->setOffice($this->officeDAOManager->findById($user->getOffice()->getId()));
        }
        return $users;
    }
  
}

